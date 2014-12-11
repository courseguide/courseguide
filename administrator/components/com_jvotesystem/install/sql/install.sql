--
-- Tabellenstruktur für Tabelle `#__jvotesystem_answers`
--

CREATE TABLE IF NOT EXISTS `#__jvotesystem_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `box_id` int(11) DEFAULT NULL,
  `answer` text NOT NULL,
  `color` varchar(6) NOT NULL,
  `published` int(11) NOT NULL,
  `autor_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `no_spam_admin` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__jvotesystem_apikeys`
--

CREATE TABLE IF NOT EXISTS `#__jvotesystem_apikeys` (
  `key` varchar(72) NOT NULL,
  `params` text NOT NULL,
  `count` int(11) NOT NULL,
  `total_count` int(11) NOT NULL,
  `last_start` datetime NOT NULL,
  `last_access` datetime NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__jvotesystem_bbcodes`
--

CREATE TABLE IF NOT EXISTS `#__jvotesystem_bbcodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `published` int(1) NOT NULL DEFAULT '1',
  `regex` text NOT NULL,
  `replace` text NOT NULL,
  `replaceNot` text,
  `withButton` int(1) NOT NULL DEFAULT '0',
  `buttonInfo` text NOT NULL,
  `editorCode` text NOT NULL,
  `buttonImage` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__jvotesystem_boxes`
--

CREATE TABLE IF NOT EXISTS `#__jvotesystem_boxes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catid` int(11) NOT NULL DEFAULT '1',
  `title` text NOT NULL,
  `question` text NOT NULL,
  `alias` varchar(25) NOT NULL,
  `access` text NOT NULL,
  `published` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `allowed_votes` int(11) NOT NULL,
  `add_answer` int(11) NOT NULL,
  `add_comment` int(1) NOT NULL,
  `created` datetime NOT NULL,
  `autor_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__jvotesystem_categories`
--

CREATE TABLE IF NOT EXISTS `#__jvotesystem_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL,
  `title` text NOT NULL,
  `alias` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `accesslevel` int(11) NOT NULL DEFAULT '1',
  `published` int(1) NOT NULL,
  `params` text NOT NULL,
  `access` text NOT NULL,
  `autor_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__jvotesystem_comments`
--

CREATE TABLE IF NOT EXISTS `#__jvotesystem_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `answer_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `autor_id` int(11) NOT NULL,
  `published` int(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `no_spam_admin` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__jvotesystem_email_tasks`
--

CREATE TABLE IF NOT EXISTS `#__jvotesystem_email_tasks` (
  `hash` varchar(72) NOT NULL,
  `params` text NOT NULL,
  `uid` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__jvotesystem_logs`
--

CREATE TABLE IF NOT EXISTS `#__jvotesystem_logs` (
  `type` varchar(10) NOT NULL,
  `time` int(11) NOT NULL,
  `time_ms` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `jvsuid` int(11) NOT NULL,
  `message` text NOT NULL,
  `pars` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__jvotesystem_sessions`
--

CREATE TABLE IF NOT EXISTS `#__jvotesystem_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `cookie` varchar(32) NOT NULL,
  `rights` int(1) NOT NULL DEFAULT '0',
  `lastVisitDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `jsession_id` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__jvotesystem_spam_reports`
--

CREATE TABLE IF NOT EXISTS `#__jvotesystem_spam_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `block_group` varchar(10) CHARACTER SET latin1 NOT NULL,
  `block_id` int(11) NOT NULL,
  `time` datetime NOT NULL,
  `msg` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__jvotesystem_tasks`
--

CREATE TABLE IF NOT EXISTS `#__jvotesystem_tasks` (
  `group` varchar(20) NOT NULL,
  `id` int(11) NOT NULL,
  PRIMARY KEY (`group`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__jvotesystem_users`
--

CREATE TABLE IF NOT EXISTS `#__jvotesystem_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jid` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(50) NOT NULL,
  `registered_time` datetime NOT NULL,
  `blocked` int(1) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__jvotesystem_votes`
--

CREATE TABLE IF NOT EXISTS `#__jvotesystem_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  `votes` int(11) NOT NULL,
  `voted_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `#__jvotesystem_bbcodes`
--

INSERT IGNORE INTO `#__jvotesystem_bbcodes` (`id`, `name`, `published`, `regex`, `replace`, `replaceNot`, `withButton`, `buttonInfo`, `editorCode`, `buttonImage`) VALUES
(1, 'Bold', 1, '/\\[b\\](.*?)\\[\\/b\\]/', '<b>$1</b>', '', 1, 'Enter text', '[b]{value}[/b]', 'bold.gif'),
(2, 'Underline', 1, '/\\[u\\](.*?)\\[\\/u\\]/', '<u>$1</u>', '', 1, 'Enter text', '[u]{value}[/u]', 'underline.gif'),
(3, 'Italic', 1, '/\\[i\\](.*?)\\[\\/i\\]/', '<i>$1</i>', '', 1, 'Enter text', '[i]{value}[/i]', 'italic.gif'),
(4, 'Image', 1, '/\\[img\\](.*?)\\[\\/img\\]/', '<br /><a href="$1" target="_blank"><img src="$1" style="border:0pt;max-width:300px;max-height:300px;margin:2px;" /></a><br />', ' ', 1, 'Enter full URL to the image', '[img]{value}[/img]', 'image.gif'),
(5, 'Url', 1, '/\\[url=([^ ]+).*\\](.*)\\[\\/url\\]/', '<a href="$1" target="_blank">$2</a>', ' ', 0, '', '', ''),
(6, 'Url', 1, '/\\[url\\](.*?)\\[\\/url\\]/', '<a href="$1" target="_blank">$1</a>', ' ', 1, 'Enter full URL', '[url]{value}[/url]', 'link.gif'),
(7, 'Soundcloud', 1, '/\\[soundcloud\\](.*?)\\[\\/soundcloud\\]/', '<object height="81" width="100%"> <param name="movie" value="http://player.soundcloud.com/player.swf?url=$1"></param> <param name="allowscriptaccess" value="always"></param> <param name="wmode" value="transparent"> <embed allowscriptaccess="always" height="81" src="http://player.soundcloud.com/player.swf?url=$1" type="application/x-shockwave-flash" width="100%"></embed> </object>', ' ', 0, '', '', ''),
(8, 'Youtube', 1, '/\\[youtube\\](.*?)\\[\\/youtube\\]/', '<iframe style="width:100%;" width="100%" height="349" src="http://www.youtube.com/embed/$1?wmode=transparent" frameborder="0" allowfullscreen></iframe>', ' ', 1, 'Enter Youtube-VideoID', '[youtube]{value}[/youtube]', 'youtube.gif'),
(9, 'Smile', 1, ':)', '<img src="{bbCodeImagePath}/smiles/smile.gif" style="margin:0;padding:0;border:0 none;" alt=":)" />', ' ', 1, '', ' :) ', 'smiles/smile.gif'),
(12, 'Laugh', 1, ':D', '<img src="{bbCodeImagePath}/smiles/laugh.gif" style="margin:0;padding:0;border:0 none;" alt=":D" />', ' ', 1, '', ' :D ', 'smiles/laugh.gif'),
(13, 'Normal', 1, ':-|', '<img src="{bbCodeImagePath}/smiles/normal.gif" style="margin:0;padding:0;border:0 none;" alt=":-|" />', ' ', 1, '', ' :-| ', 'smiles/normal.gif'),
(14, 'Sad', 1, ':(', '<img src="{bbCodeImagePath}/smiles/sad.gif" style="margin:0;padding:0;border:0 none;" alt=":(" />', ' ', 1, '', ':( ', 'smiles/sad.gif'),
(15, 'Lol', 1, ':lol:', '<img src="{bbCodeImagePath}/smiles/lol.gif" style="margin:0;padding:0;border:0 none;" alt=":lol:" />', ' ', 0, '', ' :lol: ', 'smiles/lol.gif'),
(16, 'Wink', 1, ';-)', '<img src="{bbCodeImagePath}/smiles/wink.gif" style="margin:0;padding:0;border:0 none;" alt=";-)" />', ' ', 0, '', ' ;-) ', 'smiles/wink.gif'),
(17, 'Cool', 1, '8)', '<img src="{bbCodeImagePath}/smiles/cool.gif" style="margin:0;padding:0;border:0 none;" alt="8)" />', ' ', 0, '', ' 8) ', 'smiles/cool.gif'),
(18, 'Whistling', 1, ':-*', '<img src="{bbCodeImagePath}/smiles/whistling.gif" style="margin:0;padding:0;border:0 none;" alt=":-*" />', ' ', 0, '', ' :-* ', 'smiles/whistling.gif'),
(19, 'Redface', 1, ':oops:', '<img src="{bbCodeImagePath}/smiles/redface.gif" style="margin:0;padding:0;border:0 none;" alt=":oops:" />', ' ', 0, '', ' :oops: ', 'smiles/redface.gif'),
(20, 'Cry', 1, ':cry:', '<img src="{bbCodeImagePath}/smiles/cry.gif" style="margin:0;padding:0;border:0 none;" alt=":cry:" />', ' ', 0, '', ' ;cry: ', 'smiles/cry.gif'),
(21, 'Surprised', 1, ':o', '<img src="{bbCodeImagePath}/smiles/surprised.gif" style="margin:0;padding:0;border:0 none;" alt=":o" />', ' ', 0, '', ' :o ', 'smiles/surprised.gif'),
(22, 'Confused', 1, ':-?', '<img src="{bbCodeImagePath}/smiles/confused.gif" style="margin:0;padding:0;border:0 none;" alt=":-?" />', ' ', 0, '', ' :-? ', 'smiles/confused.gif'),
(23, 'Sick', 1, ':-x', '<img src="{bbCodeImagePath}/smiles/sick.gif" style="margin:0;padding:0;border:0 none;" alt=":-x" />', ' ', 0, '', ' :-x ', 'smiles/sick.gif'),
(24, 'Shocked', 1, ':eek:', '<img src="{bbCodeImagePath}/smiles/shocked.gif" style="margin:0;padding:0;border:0 none;" alt=":eek:" />', ' ', 0, '', ' :eek: ', 'smiles/shocked.gif'),
(25, 'Sleeping', 1, ':zzz', '<img src="{bbCodeImagePath}/smiles/sleeping.gif" style="margin:0;padding:0;border:0 none;" alt=":zzz" />', ' ', 0, '', ' :zzz ', 'smiles/sleeping.gif'),
(26, 'Tongue', 1, ':P', '<img src="{bbCodeImagePath}/smiles/tongue.gif" style="margin:0;padding:0;border:0 none;" alt=":P" />', ' ', 0, '', ' :P ', 'smiles/tongue.gif'),
(27, 'Rolleyes', 1, ':roll:', '<img src="{bbCodeImagePath}/smiles/rolleyes.gif" style="margin:0;padding:0;border:0 none;" alt=":roll:" />', ' ', 0, '', ' :roll: ', 'smiles/rolleyes.gif'),
(28, 'Unsure', 1, ':sigh:', '<img src="{bbCodeImagePath}/smiles/unsure.gif" style="margin:0;padding:0;border:0 none;" alt=":sigh:" />', ' ', 0, '', ' :sigh: ', 'smiles/unsure.gif');

