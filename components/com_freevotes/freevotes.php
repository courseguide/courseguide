<?php
/*
* @name freevote 1.0
* Created By Guarneri Iacopo
* http://www.the-html-tool.com/
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.module.helper' );
$module = JModuleHelper::getModule('mod_freevotes');
$moduleParams = new JRegistry();
$moduleParams->loadString($module->params);
$modifica_voti  = $moduleParams->get('modifica_voti', '0');
$database = JFactory::getDBO();
$id_user = JFactory::getUser()->get('id');
if(JRequest::getVar('risposta', '', 'post')!="" && JRequest::getVar('voto', '', 'post')!=""){
    if($id_user==0){
        echo("ERROR_USER");
        JFactory::getApplication()->close();
    }
    else{
        $query = "SELECT id FROM #__free_votes_risposte_user WHERE risposta=".JRequest::getVar('risposta', '', 'post')." AND id_user=".$id_user;
        $database->setQuery($query);
        $results_voti = $database->loadAssocList();
        $ins_up = count($results_voti);
        if($ins_up==0 || $ins_up==""){
            //inserisci voto
            echo "NUOVO_VOTO";
            $query='INSERT INTO #__free_votes_risposte_user (id_user,risposta,voto) VALUES ('.$id_user.','.JRequest::getVar('risposta', '', 'post').','.JRequest::getVar('voto', '', 'post').')';
        }else{
            if($modifica_voti==0){
                echo "ERROR_VOTO";
                JFactory::getApplication()->close();
            }else{
                //aggiorna voto
                echo "AGGIORNATO";
                $query='UPDATE #__free_votes_risposte_user SET voto='.JRequest::getVar('voto', '', 'post').' WHERE id_user='.$id_user.' && id='.$results_voti[0]['id'];
            }
        }
        echo '[RETURN]{"risposta":"'.JRequest::getVar('risposta', '', 'post').'","voto":"'.JRequest::getVar('voto', '', 'post').'"}';
        $database->setQuery($query);
        $database->query();
    }
}
if(JRequest::getVar('aggiorna', '', 'post')!=""){
    //estrapolo le percentuali dei voti
    
    $query = "SELECT * FROM #__free_votes_risposte_user AS A, #__free_votes_risposte AS B WHERE B.domanda='".JRequest::getVar('aggiorna', '', 'post')."' AND A.risposta=B.id ORDER BY B.id desc";
    $database->setQuery($query);
    $results = $database->loadAssocList();
    $precedente=""; $somma=Array();
    foreach($results as $result)
    {
        if($result['nome']!=$precedente)
        {
            // Viktor:
            // The last element ("1") is added to calculate average afterwards
            // It represents the number of users voted
            $somma[]=Array($result['nome'],$result['voto'],$result['colore'], 1);
            $precedente=$result['nome'];
        }
        else
        {
            $somma[count($somma)-1][1]+=$result['voto'];
            // Viktor:
            // Increment with 1 for every new vote
            $somma[count($somma)-1][3]+=1;
        }
    }
    
    for($i=0;$i<count($somma);$i++)
    {   
        // Viktor:
        // The original value of y was without rounding and without divising.
        // It represented the total votes.
        echo"
        browserData.push({
            name:'".addslashes($somma[$i][0])."',
            y:".round($somma[$i][1] / $somma[$i][3], 2).",
            color:'".addslashes($somma[$i][2])."'
        });";
    }
}
JFactory::getApplication()->close();