  <?php
		
		/*
        ================================================================================
        EvalMath - PHP Class to safely evaluate math expressions
        Copyright (C) 2005 Miles Kaufmann <http://www.twmagic.com/>
        ================================================================================

        NAME
                EvalMath - safely evaluate math expressions

        SYNOPSIS
                <?
                  include('evalmath.class.php');
                  $m = new EvalMath;
                  // basic evaluation:
                  $result = $m->evaluate('2+2');
                  // supports: order of operation; parentheses; negation; built-in functions
                  $result = $m->evaluate('-8(5/2)^2*(1-sqrt(4))-8');
                  // create your own variables
                  $m->evaluate('a = e^(ln(pi))');
                  // or functions
                  $m->evaluate('f(x,y) = x^2 + y^2 - 2x*y + 1');
                  // and then use them
                  $result = $m->evaluate('3*f(42,a)');
                ?>

        DESCRIPTION
                Use the EvalMath class when you want to evaluate mathematical expressions
                from untrusted sources.  You can define your own variables and functions,
                which are stored in the object.  Try it, it's fun!

        METHODS
                $m->evalute($expr)
                        Evaluates the expression and returns the result.  If an error occurs,
                        prints a warning and returns false.  If $expr is a function assignment,
                        returns true on success.

                $m->e($expr)
                        A synonym for $m->evaluate().

                $m->vars()
                        Returns an associative array of all user-defined variables and values.

                $m->funcs()
                        Returns an array of all user-defined functions.

        PARAMETERS
                $m->suppress_errors
                        Set to true to turn off warnings when evaluating expressions

                $m->last_error
                        If the last evaluation failed, contains a string describing the error.
                        (Useful when suppress_errors is on).

        AUTHOR INFORMATION
                Copyright 2005, Miles Kaufmann.


       @license	 http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
       */
		
		defined('_JEXEC') or die('Restricted access');

		class EvalMath
		{

			var $suppress_errors = false;

			var $last_error = null;

			var $v = array('e' => 2.718281828459045, 'pi' => 3.141592653589793); // variables (and constants)

			
			var $f = array(); // user-defined functions

			
			var $vb = array('e', 'pi'); // constants

			
			var $fb = array( // built-in functions
				'sin', 
				'sinh', 
				'arcsin', 
				'asin', 
				'arcsinh', 
				'asinh', 
				'cos', 
				'cosh', 
				'arccos', 
				'acos', 
				'arccosh', 
				'acosh', 
				'tan', 
				'tanh', 
				'arctan', 
				'atan', 
				'arctanh', 
				'atanh', 
				'sqrt', 
				'abs', 
				'ln', 
				'log10', 
				'log');

			function EvalMath()
			{
				// make the variables a little more accurate
				$this->v['pi'] = pi();
				$this->v['e'] = exp(1);
			}

			function e($expr)
			{
				return $this->evaluate($expr);
			}

			function evaluate($expr)
			{
				$this->last_error = null;
				$expr = trim($expr);
				if (substr($expr, -1, 1) == ';')
					$expr = substr($expr, 0, strlen($expr) - 1); // strip semicolons at the end
				//===============
				// is it a variable assignment?
				if (preg_match('/^\s*([a-z]\w*)\s*=\s*(.+)$/', $expr, $matches))
				{
					if (in_array($matches[1], $this->vb))
					{ // make sure we're not assigning to a constant
						return $this->trigger("cannot assign to constant '$matches[1]'");
					}
					if (($tmp = $this->pfx($this->nfx($matches[2]))) === false)
						return false; // get the result and make sure it's good
					$this->v[$matches[1]] = $tmp; // if so, stick it in the variable array
					return $this->v[$matches[1]]; // and return the resulting value
				//===============
				// is it a function assignment?
				}
				elseif (preg_match('/^\s*([a-z]\w*)\s*\(\s*([a-z]\w*(?:\s*,\s*[a-z]\w*)*)\s*\)\s*=\s*(.+)$/', $expr, $matches))
				{
					$fnn = $matches[1]; // get the function name
					if (in_array($matches[1], $this->fb))
					{ // make sure it isn't built in
						return $this->trigger("cannot redefine built-in function '$matches[1]()'");
					}
					$args = explode(",", preg_replace("/\s+/", "", $matches[2])); // get the arguments
					if (($stack = $this->nfx($matches[3])) === false)
						return false; // see if it can be converted to postfix
					for ($i = 0; $i < count($stack); $i++)
					{ // freeze the state of the non-argument variables
						$token = $stack[$i];
						if (preg_match('/^[a-z]\w*$/', $token) and !in_array($token, $args))
						{
							if (array_key_exists($token, $this->v))
							{
								$stack[$i] = $this->v[$token];
							}
							else
							{
								return $this->trigger("undefined variable '$token' in function definition");
							}
						}
					}
					$this->f[$fnn] = array('args' => $args, 'func' => $stack);
					return true;
				
		//===============
				}
				else
				{
					return $this->pfx($this->nfx($expr)); // straight up evaluation, woo
				}
			}

			function vars()
			{
				$output = $this->v;
				unset($output['pi']);
				unset($output['e']);
				return $output;
			}

			function funcs()
			{
				$output = array();
				foreach ($this->f as $fnn => $dat)
					$output[] = $fnn . '(' . implode(',', $dat['args']) . ')';
				return $output;
			}

			//===================== HERE BE INTERNAL METHODS ====================\\
			

			// Convert infix to postfix notation
			function nfx($expr)
			{
				
				$index = 0;
				$stack = new EvalMathStack();
				$output = array(); // postfix form of expression, to be passed to pfx()
				$expr = trim(strtolower($expr));
				
				$ops = array('+', '-', '*', '/', '^', '_');
				$ops_r = array('+' => 0, '-' => 0, '*' => 0, '/' => 0, '^' => 1); // right-associative operator?
				$ops_p = array('+' => 0, '-' => 0, '*' => 1, '/' => 1, '_' => 1, '^' => 2); // operator precedence
				

				$expecting_op = false; // we use this in syntax-checking the expression
				// and determining when a - is a negation
				

				if (preg_match("/[^\w\s+*^\/()\.,-]/", $expr, $matches))
				{ // make sure the characters are all good
					return $this->trigger("illegal character '{$matches[0]}'");
				}
				
				while (1)
				{ // 1 Infinite Loop ;)
					$op = substr($expr, $index, 1); // get the first character at the current index
					// find out if we're currently at the beginning of a number/variable/function/parenthesis/operand
					$ex = preg_match('/^([a-z]\w*\(?|\d+(?:\.\d*)?|\.\d+|\()/', substr($expr, $index), $match);
					//===============
					if ($op == '-' and !$expecting_op)
					{ // is it a negation instead of a minus?
						$stack->push('_'); // put a negation on the stack
						$index++;
					}
					elseif ($op == '_')
					{ // we have to explicitly deny this, because it's legal on the stack
						return $this->trigger("illegal character '_'"); // but not in the input expression
					//===============
					}
					elseif ((in_array($op, $ops) or $ex) and $expecting_op)
					{ // are we putting an operator on the stack?
						if ($ex)
						{ // are we expecting an operator but have a number/variable/function/opening parethesis?
							$op = '*';
							$index--; // it's an implicit multiplication
						}
						// heart of the algorithm:
						while ($stack->count > 0 and ($o2 = $stack->last()) and in_array($o2, $ops) and ($ops_r[$op] ? $ops_p[$op] < $ops_p[$o2] : $ops_p[$op] <= $ops_p[$o2]))
						{
							$output[] = $stack->pop(); // pop stuff off the stack into the output
						}
						// many thanks: http://en.wikipedia.org/wiki/Reverse_Polish_notation#The_algorithm_in_detail
						$stack->push($op); // finally put OUR operator onto the stack
						$index++;
						$expecting_op = false;
					
		//===============
					}
					elseif ($op == ')' and $expecting_op)
					{ // ready to close a parenthesis?
						while (($o2 = $stack->pop()) != '(')
						{ // pop off the stack back to the last (
							if (is_null($o2))
								return $this->trigger("unexpected ')'");
							else
								$output[] = $o2;
						}
						if (preg_match("/^([a-z]\w*)\($/", $stack->last(2), $matches))
						{ // did we just close a function?
							$fnn = $matches[1]; // get the function name
							$arg_count = $stack->pop(); // see how many arguments there were (cleverly stored on the stack, thank you)
							$output[] = $stack->pop(); // pop the function and push onto the output
							if (in_array($fnn, $this->fb))
							{ // check the argument count
								if ($arg_count > 1)
									return $this->trigger("too many arguments ($arg_count given, 1 expected)");
							}
							elseif (array_key_exists($fnn, $this->f))
							{
								if ($arg_count != count($this->f[$fnn]['args']))
									return $this->trigger("wrong number of arguments ($arg_count given, " . count($this->f[$fnn]['args']) . " expected)");
							}
							else
							{ // did we somehow push a non-function on the stack? this should never happen
								return $this->trigger("internal error");
							}
						}
						$index++;
					
		//===============
					}
					elseif ($op == ',' and $expecting_op)
					{ // did we just finish a function argument?
						while (($o2 = $stack->pop()) != '(')
						{
							if (is_null($o2))
								return $this->trigger("unexpected ','"); // oops, never had a (
							else
								$output[] = $o2; // pop the argument expression stuff and push onto the output
						}
						// make sure there was a function
						if (!preg_match("/^([a-z]\w*)\($/", $stack->last(2), $matches))
							return $this->trigger("unexpected ','");
						$stack->push($stack->pop() + 1); // increment the argument count
						$stack->push('('); // put the ( back on, we'll need to pop back to it again
						$index++;
						$expecting_op = false;
					
		//===============
					}
					elseif ($op == '(' and !$expecting_op)
					{
						$stack->push('('); // that was easy
						$index++;
						$allow_neg = true;
					
		//===============
					}
					elseif ($ex and !$expecting_op)
					{ // do we now have a function/variable/number?
						$expecting_op = true;
						$val = $match[1];
						if (preg_match("/^([a-z]\w*)\($/", $val, $matches))
						{ // may be func, or variable w/ implicit multiplication against parentheses...
							if (in_array($matches[1], $this->fb) or array_key_exists($matches[1], $this->f))
							{ // it's a func
								$stack->push($val);
								$stack->push(1);
								$stack->push('(');
								$expecting_op = false;
							}
							else
							{ // it's a var w/ implicit multiplication
								$val = $matches[1];
								$output[] = $val;
							}
						}
						else
						{ // it's a plain old var or num
							$output[] = $val;
						}
						$index += strlen($val);
					
		//===============
					}
					elseif ($op == ')')
					{ // miscellaneous error checking
						return $this->trigger("unexpected ')'");
					}
					elseif (in_array($op, $ops) and !$expecting_op)
					{
						return $this->trigger("unexpected operator '$op'");
					}
					else
					{ // I don't even want to know what you did to get here
						return $this->trigger("an unexpected error occured");
					}
					if ($index == strlen($expr))
					{
						if (in_array($op, $ops))
						{ // did we end with an operator? bad.
							return $this->trigger("operator '$op' lacks operand");
						}
						else
						{
							break;
						}
					}
					while (substr($expr, $index, 1) == ' ')
					{ // step the index past whitespace (pretty much turns whitespace
						$index++; // into implicit multiplication if no operator is there)
					}
				
				}
				while (!is_null($op = $stack->pop()))
				{ // pop everything off the stack and push onto output
					if ($op == '(')
						return $this->trigger("expecting ')'"); // if there are (s on the stack, ()s were unbalanced
					$output[] = $op;
				}
				return $output;
			}

			// evaluate postfix notation
			function pfx($tokens, $vars = array())
			{
				
				if ($tokens == false)
					return false;
				
				$stack = new EvalMathStack();
				
				foreach ($tokens as $token)
				{ // nice and easy
					// if the token is a binary operator, pop two values off the stack, do the operation, and push the result back on
					if (in_array($token, array('+', '-', '*', '/', '^')))
					{
						if (is_null($op2 = $stack->pop()))
							return $this->trigger("internal error");
						if (is_null($op1 = $stack->pop()))
							return $this->trigger("internal error");
						switch ($token)
						{
							case '+':
								$stack->push($op1 + $op2);
								break;
							case '-':
								$stack->push($op1 - $op2);
								break;
							case '*':
								$stack->push($op1 * $op2);
								break;
							case '/':
								if ($op2 == 0)
									return $this->trigger("division by zero");
								$stack->push($op1 / $op2);
								break;
							case '^':
								$stack->push(pow($op1, $op2));
								break;
						}
					
		// if the token is a unary operator, pop one value off the stack, do the operation, and push it back on
					}
					elseif ($token == "_")
					{
						$stack->push(-1 * $stack->pop());
					
		// if the token is a function, pop arguments off the stack, hand them to the function, and push the result back on
					}
					elseif (preg_match("/^([a-z10]\w*)\($/", $token, $matches))
					{ // it's a function!
						$fnn = $matches[1];
						if (in_array($fnn, $this->fb))
						{ // built-in function:
							if (is_null($op1 = $stack->pop()))
								return $this->trigger("internal error");
							$fnn = preg_replace("/^arc/", "a", $fnn); // for the 'arc' trig synonyms
							if ($fnn == 'ln')
								$fnn = 'log';
							eval('$stack->push(' . $fnn . '($op1));'); // perfectly safe eval()
						}
						elseif (array_key_exists($fnn, $this->f))
						{ // user function
							// get args
							$args = array();
							for ($i = count($this->f[$fnn]['args']) - 1; $i >= 0; $i--)
							{
								if (is_null($args[$this->f[$fnn]['args'][$i]] = $stack->pop()))
									return $this->trigger("internal error");
							}
							$stack->push($this->pfx($this->f[$fnn]['func'], $args)); // yay... recursion!!!!
						}
					
		// if the token is a number or variable, push it on the stack
					}
					else
					{
						if (is_numeric($token))
						{
							$stack->push($token);
						}
						elseif (array_key_exists($token, $this->v))
						{
							$stack->push($this->v[$token]);
						}
						elseif (array_key_exists($token, $vars))
						{
							$stack->push($vars[$token]);
						}
						else
						{
							return $this->trigger("undefined variable '$token'");
						}
					}
				}
				// when we're out of tokens, the stack should have a single element, the final result
				if ($stack->count != 1)
					return $this->trigger("internal error");
				return $stack->pop();
			}

			// trigger an error, but nicely, if need be
			function trigger($msg)
			{
				$this->last_error = $msg;
				if (!$this->suppress_errors)
					trigger_error($msg, E_USER_WARNING);
				return false;
			}
		}

		// for internal use
		class EvalMathStack
		{

			var $stack = array();

			var $count = 0;

			function push($val)
			{
				$this->stack[$this->count] = $val;
				$this->count++;
			}

			function pop()
			{
				if ($this->count > 0)
				{
					$this->count--;
					return $this->stack[$this->count];
				}
				return null;
			}

			function last($n = 1)
			{
				if ($this->count - $n >= 0)
					return $this->stack[$this->count - $n];
			}
		}

		function calcR($data, $calc_data)
		{
			$sum_Squares_tot = 0;
			$sum_Squares_err = 0;
			$sum_data = array_sum($data);
			
			$Y_mean = $sum_data / count($data);
			
			for ($i = 0; $i < count($data); $i++)
				$sum_Squares_tot += pow(($data[$i] - $Y_mean), 2);
			
			for ($i = 0; $i < count($data); $i++)
				$sum_Squares_err += pow(($data[$i] - $calc_data[$i]), 2);
			
			$R_square = 1 - ($sum_Squares_err / $sum_Squares_tot);
			
			return $R_square;
		}

		class SimpleLinearRegression
		{

			var $n;

			var $X = array();

			var $Y = array();

			var $ConfInt;

			var $Alpha;

			var $XMean;

			var $YMean;

			var $SumXX;

			var $SumXY;

			var $SumYY;

			var $Slope;

			var $YInt;

			var $PredictedY = array();

			var $Error = array();

			var $SquaredError = array();

			var $TotalError;

			var $SumError;

			var $SumSquaredError;

			var $ErrorVariance;

			var $StdErr;

			var $SlopeStdErr;

			var $SlopeVal; // T value of Slope

			
			var $YIntStdErr;

			var $YIntTVal; // T value for Y Intercept

			
			var $R;

			var $RSquared;

			var $DF; // Degrees of Freedom

			
			var $SlopeProb; // Probability of Slope Estimate

			
			var $YIntProb; // Probability of Y Intercept Estimate

			
			var $AlphaTVal; // T Value for given alpha setting

			
			var $ConfIntOfSlope;

			var $RPath = "/usr/local/bin/R"; // Your path here

			
			var $format = "%01.2f"; // Used for formatting output

			
			function SimpleLinearRegression($X, $Y, $ConfidenceInterval = "95")
			{
				
				$numX = count($X);
				$numY = count($Y);
				
				if ($numX != $numY)
					return null;
				
				if ($numX <= 1)
					return null;
				
				$this->n = $numX;
				
				$this->X = $X;
				$this->Y = $Y;
				
				$this->ConfInt = $ConfidenceInterval;
				//$this->Alpha = (1 + ($this->ConfInt / 100)) / 2;
				

				$this->XMean = $this->getMean($this->X);
				$this->YMean = $this->getMean($this->Y);
				$this->SumXX = $this->getSumXX();
				$this->SumYY = $this->getSumYY();
				$this->SumXY = $this->getSumXY();
				$this->Slope = $this->getSlope();
				$this->YInt = $this->getYInt();
				$this->PredictedY = $this->getPredictedY();
				//$this->Error = $this->getError();
				//$this->SquaredError = $this->getSquaredError();
				//$this->SumError = $this->getSumError();
				//($this->TotalError = $this->getTotalError();
				//$this->SumSquaredError = $this->getSumSquaredError();
				//$this->ErrorVariance = $this->getErrorVariance();
				//$this->StdErr = $this->getStdErr();
				//$this->SlopeStdErr = $this->getSlopeStdErr();
				//$this->YIntStdErr = $this->getYIntStdErr();
				//$this->SlopeTVal = $this->getSlopeTVal();
				//$this->YIntTVal = $this->getYIntTVal();
				$this->R = $this->getR();
				$this->RSquared = $this->getRSquared();
				//$this->DF = $this->getDF();
				//$this->SlopeProb = $this->getStudentProb($this->SlopeTVal, $this->DF);
				//$this->YIntProb = $this->getStudentProb($this->YIntTVal, $this->DF);
				//$this->AlphaTVal = $this->getInverseStudentProb($this->Alpha, $this->DF);
				//$this->ConfIntOfSlope = $this->getConfIntOfSlope();
				

				return true;
			}

			function getMean($data)
			{
				$mean = 0.0;
				$sum = 0.0;
				$k = 0;
				for ($i = 0; $i < $this->n; $i++)
				{
					$sum += $data[$i];
					if ($data[$i] != null)
						$k++;
				}
				$mean = $sum / $this->n;
				return $mean;
			}

			function getSumXX()
			{
				$SumXX = 0.0;
				for ($i = 0; $i < $this->n; $i++)
				{
					$SumXX += ($this->X[$i] - $this->XMean) * ($this->X[$i] - $this->XMean);
				}
				return $SumXX;
			}

			function getSumYY()
			{
				$SumYY = 0.0;
				for ($i = 0; $i < $this->n; $i++)
				{
					$SumYY += ($this->Y[$i] - $this->YMean) * ($this->Y[$i] - $this->YMean);
				}
				return $SumYY;
			}

			function getSumXY()
			{
				$SumXY = 0.0;
				for ($i = 0; $i < $this->n; $i++)
				{
					$SumXY += ($this->X[$i] - $this->XMean) * ($this->Y[$i] - $this->YMean);
				}
				return $SumXY;
			}

			function getSlope()
			{
				$Slope = 0.0;
				$Slope = $this->SumXY / $this->SumXX;
				return $Slope;
			}

			function getYInt()
			{
				$YInt = 0.0;
				$YInt = $this->YMean - ($this->Slope * $this->XMean);
				return $YInt;
			}

			function getPredictedY()
			{
				for ($i = 0; $i < $this->n; $i++)
				{
					$PredictedY[$i] = $this->YInt + ($this->Slope * $this->X[$i]);
				}
				return $PredictedY;
			}

			function getError()
			{
				$Error = array();
				for ($i = 0; $i < $this->n; $i++)
				{
					$Error[$i] = $this->Y[$i] - $this->PredictedY[$i];
				}
				return $Error;
			}

			function getTotalError()
			{
				$TotalError = 0.0;
				for ($i = 0; $i < $this->n; $i++)
				{
					$TotalError += pow(($this->Y[$i] - $this->YMean), 2);
				}
				return $TotalError;
			}

			function getSquaredError()
			{
				$SquaredError = array();
				for ($i = 0; $i < $this->n; $i++)
				{
					$SquaredError[$i] = pow(($this->Y[$i] - $this->PredictedY[$i]), 2);
				}
				return $SquaredError;
			}

			function getSumError()
			{
				$SumError = 0.0;
				for ($i = 0; $i < $this->n; $i++)
				{
					$SumError += $this->Error[$i];
				}
				return $SumError;
			}

			function getSumSquaredError()
			{
				$SumSquaredError = 0.0;
				for ($i = 0; $i < $this->n; $i++)
				{
					$SumSquaredError += $this->SquaredError[$i];
				}
				return $SumSquaredError;
			}

			function getErrorVariance()
			{
				$ErrorVariance = 0.0;
				$ErrorVariance = $this->SumSquaredError / ($this->n - 2);
				return $ErrorVariance;
			}

			function getStdErr()
			{
				$StdErr = 0.0;
				$StdErr = sqrt($this->ErrorVariance);
				return $StdErr;
			}

			function getSlopeStdErr()
			{
				$SlopeStdErr = 0.0;
				$SlopeStdErr = $this->StdErr / sqrt($this->SumXX);
				return $SlopeStdErr;
			}

			function getYIntStdErr()
			{
				$YIntStdErr = 0.0;
				$YIntStdErr = $this->StdErr * sqrt(1 / $this->n + pow($this->XMean, 2) / $this->SumXX);
				return $YIntStdErr;
			}

			function getSlopeTVal()
			{
				$SlopeTVal = 0.0;
				if ($this->SlopeStdErr != 0)
					$SlopeTVal = $this->Slope / $this->SlopeStdErr;
				return $SlopeTVal;
			}

			function getYIntTVal()
			{
				$YIntTVal = 0.0;
				if ($this->YIntStdErr != 0)
					$YIntTVal = $this->YInt / $this->YIntStdErr;
				return $YIntTVal;
			}

			function getR()
			{
				$R = 0.0;
				$R = $this->SumXY / sqrt($this->SumXX * $this->SumYY);
				return $R;
			}

			function getRSquared()
			{
				$RSquared = 0.0;
				$RSquared = $this->R * $this->R;
				return $RSquared;
			}

			function getDF()
			{
				$DF = 0.0;
				$DF = $this->n - 2;
				return $DF;
			}

			function getStudentProb($T, $df)
			{
				$Probability = 0.0;
				$cmd = "echo 'dt($T, $df)' | $this->RPath --slave";
				$result = shell_exec($cmd);
				list ($LineNumber, $Probability) = explode(" ", trim($result));
				return $Probability;
			}

			function getInverseStudentProb($alpha, $df)
			{
				$InverseProbability = 0.0;
				$cmd = "echo 'qt($alpha, $df)' | $this->RPath --slave";
				$result = shell_exec($cmd);
				list ($LineNumber, $InverseProbability) = explode(" ", trim($result));
				return $InverseProbability;
			}

			function getConfIntOfSlope()
			{
				$ConfIntOfSlope = 0.0;
				$ConfIntOfSlope = $this->AlphaTVal * $this->SlopeStdErr;
				return $ConfIntOfSlope;
			}
		
		}
		?>