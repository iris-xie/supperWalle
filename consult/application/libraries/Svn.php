<?php

/**
 * Class Svn
 */
class SVN {
    static private $_account = NULL;
    static private $_password = NULL;
    static private $_root_directory = NULL;
    static private $_last_query_cmd = NULL;
	static private $_last_cmd_result = NULL;
	static private $_last_cmd_status = NULL;
    static private $_limit = 100;
	static private $_repos_dir = NULL;
    public function __construct() {
		
    }

    static public function config($account, $pwd, $dir) {
        self::$_account = $account;
        self::$_password = $pwd;
        self::$_root_directory = $dir;
    }

    static public function ls($path) {
        $command = 'svn ls '.$path.' --xml';
        $output = self::run_cmd($command);
        return $output;
    }

    static public function diff($version, $dir = '', $summarize = true) {
        if ($summarize === true) {
			if (is_array($dir)) {
				$dir = implode(' ', $dir);
			}
            $output = self::run_cmd("svn diff {$dir} -r$version  --summarize --xml");
            $string = implode('', $output);
            $xml = self::xml_parser($string);
            if ($xml === false)
                return false;
            $data = array();
            foreach ($xml as $val) {
                foreach ($val->path as $v) {
                    $attributes = $v->attributes();
                    $data[] = array(
                        'name' => (string)$v,
                        'item' => (string)$attributes->item,
                        'props' => (string)$attributes->props,
                        'kind' => (string)$attributes->kind,
                    );
                }
            }
            return $data;
        } else {
            $output = self::run_cmd("svn diff {$dir} -r$version");
            return $output;
        }
    }

    /**
     * @param $path
     * @return mixed
     */
    static public function info($path = '', $server_info = true) {
        $command = "svn info --xml";
        $output = self::run_cmd($command);
        $string = implode('', $output);
        $xml = self::xml_parser($string);
        if ($xml === false)
            return false;
        $root = $xml->entry->url;
		self::$_repos_dir = $root;
        $attributes = $xml->entry->commit->attributes();
        $revision = $attributes['revision'];
        $server_log = array();
        if ($server_info === true && $root) {
            $server_log = self::log($root);
        }
        return array(
            'revision' => $revision,
            'root' => $root,
            'server_log' => $server_log,
        );
    }


    static public function file_info($file, $root = '') {
        $command = "svn info {$file} --xml";
        $output = self::run_cmd($command);
        $string = implode('', $output);
        $xml = self::xml_parser($string);
        if ($xml === false) {
            $command = "svn info {$root}/{$file} --xml";
            $output = self::run_cmd($command);
            $string = implode('', $output);
            $xml = self::xml_parser($string);
            if ($xml === false)
                return false;
        }
        $url = $xml->entry->url;
        $commit = $xml->entry->commit;
        $author = $commit->author;
        $date = $commit->date;
        $attributes = $xml->entry->commit->attributes();
        return array(
            'url' => (string)$url,
            'revision' => (string)$attributes['revision'],
            'author' => (string)$author,
            'date' => date('Y-m-d H:i:s', strtotime($date)),
        );
    }

	static public function wc_file_info($file = '') {
		$command = "svn info {$file} --xml";
        $output = self::run_cmd($command);
        $string = implode('', $output);
        $xml = self::xml_parser($string);
		if ($xml === false)
			return false;
		$url = $xml->entry->url;
		$entry_attrs = $xml->entry->attributes();
        $commit = $xml->entry->commit;
        $author = $commit->author;
        $date = $commit->date;
        $attributes = $xml->entry->commit->attributes();
        return array(
            'url' => (string)$url,
			'r' => (string)$entry_attrs['revision'],
            'revision' => (string)$attributes['revision'],
            'author' => (string)$author,
            'date' => date('Y-m-d H:i:s', strtotime($date)),
        );
	}

	static public function repos_file_info() {
			
	} 

    static public function log($url = '') {
        $command = "svn log {$url} --xml --limit ".self::$_limit;
        $output = self::run_cmd($command);
		//echo Svn::last_cmd();
		//print_r($output);
        $string = implode('', $output);
        $xml = self::xml_parser($string);
        if ($xml === false)
            return false;
        $return = array();
        foreach ($xml->logentry as $logentry) {
            $attributes = $logentry->attributes();
            $return[] = array(
                'author' => (string)$logentry->author,
                'date' => strtotime($logentry->date),
                'msg' => (string)$logentry->msg,
                'revision' => (string)$attributes['revision'],
            );
        }
        return $return;
    }

    static public function update($version, $path = array()) {
        if (is_array($path))
            $path = implode(' ', $path);
        $command = "svn cleanup && svn up --force -r{$version} ".$path;
        $output = self::run_cmd($command);
        if (empty($output)) {
            return false;
        }
        return $output;
    }

    static public function copy($src, $dst, $comment) {
        $command = "svn cp $src $dst -m '$comment'";
        $output = self::run_cmd($command);
        $output = implode("<br>", $output);

        if (strpos($output, 'Committed revision')) {
            return true;
        }

        return "<br>" . $command . "<br>" . $output;
    }

    static public function delete($url, $comment) {
        $command = "svn del $url -m '$comment'";
        $output = self::run_cmd($command);
        $output = implode('<br>', $output);
        if (strpos($output, 'Committed revision')) {
            return true;
        }

        return "<br>" . $command . "<br>" . $output;
    }

    static public function move($src, $dst, $comment)
    {
        $command = "svn mv $src $dst -m '$comment'";
        $output = self::run_cmd($command);
        $output = implode('<br>', $output);

        if (strpos($output, 'Committed revision')) {
            return true;
        }
        return "<br>" . $command . "<br>" . $output;
    }

    static public function mkdir($url, $comment) {
        $command = "svn mkdir $url -m '$comment'";
        $output = self::run_cmd($command);
        $output = implode('<br>', $output);

        if (strpos($output, 'Committed revision')) {
            return true;
        }

        return "<br>" . $command . "<br>" . $output;
    }

    static public function checkout($url, $dir) {
        $command = "svn co $url";
        $output = self::run_cmd($command);
        $output = implode('<br>', $output);
        if (strstr($output, 'Checked out revision')) {
            return true;
        }
        return "<br>" . $command . "<br>" . $output;
    }

    static public function merge($revision, $url, $dir)
    {
        $command = "cd $dir && svn merge -r1:$revision $url";
        $output = implode('<br>', self::run_cmd($command));
        if (strstr($output, 'Text conflicts')) {
            return 'Command: ' . $command .'<br>'. $output;
        }
        return true;
    }

    static public function commit($dir, $comment) {
        $command = "cd $dir && svn commit -m'$comment'";
        $output = implode('<br>', self::run_cmd($command));

        if (strpos($output, 'Committed revision') || empty($output)) {
            return true;
        }
        return $output;
    }

    static public function getStatus($dir) {
        $command = 'svn st';
        return self::run_cmd($command);
    }

    static public function hasConflict($dir) {
        $output = self::getStatus($dir);
        foreach ($output as $line){
            if ('C' == substr(trim($line), 0, 1) || ('!' == substr(trim($line), 0, 1))){
                return true;
            }
        }
        return false;
    }

    static public function get_log($path) {
        $command = "svn log $path --xml";
        $output = self::run_cmd($command);
        return implode('', $output);
    }

    static function status($revision, $default_revision = 0) {
        $command = "svn status -r{$revision} -u --xml";
        $output = self::run_cmd($command);
        $string = implode('', $output);
        $xml = self::xml_parser($string);
        if ($xml === false)
            return false;
		$target = $xml->target;
		$data = array();
		foreach ($target->entry as $entry) {
			$entry_attri = $entry->attributes();
			$wc_status = 'wc-status';
			$repos_status = 'repos-status';
			$wc_status_attri = isset($entry->{$wc_status}) ? $entry->{$wc_status}->attributes() : array();
			$repos_status_attri = isset($entry->{$repos_status}) ? $entry->{$repos_status}->attributes() : array();
			$kind = self::get_file_type(self::$_root_directory, (string)$entry_attri['path']);
			$item = isset($repos_status_attri['item']) ? (string)$repos_status_attri['item'] : '';
			$props = isset($repos_status_attri['props']) ? (string)$repos_status_attri['props'] : '';
			$r = intval(!empty($wc_status_attri['revision']) ? $wc_status_attri['revision'] : $default_revision);
			if ($kind == 'dir' && $item == 'modified')
				continue;
			$data[] = array(
				'name' => (string)$entry_attri['path'],
				'item' => $item,
				'props' => $props,
				'kind' => $kind,
				'revision' => $r,
			);
		}
		return $data;
    }

	static public function get_file_type($pre_dir, $file_name) {
		$full_file_name = $pre_dir.'/'.$file_name;
		if (file_exists($full_file_name)) {
			if (is_dir($full_file_name)) {
				$kind = 'dir';
			} else {
				$kind = 'file';
			}
		} else {
			$file_name = substr($file_name, strripos($file_name, '/') + 1);
			if (strpos($file_name, '.') !== false) {
				$kind = 'file';
			} else {
				$kind = 'dir';
			}
		}
		return $kind;
	}

    static public function get_head_revision($path) {
        $command = "cd $path && svn up";
        $output = self::run_cmd($command);
        $output = implode('<br>', $output);

        preg_match_all("/[0-9]+/", $output, $ret);
        if (!$ret[0][0]){
        return "<br>" . $command . "<br>" . $output;
        }

        return $ret[0][0];
    }

	static public function get_repos_dir() {
		return self::$_repos_dir;
	}

    /**
     * exec command
     * @param $command
     * @param bool|TRUE $cd
     * @return mixed
     */
    static protected function run_cmd($command, $cd = TRUE) {
        $cdCommand = $cd === TRUE ? 'cd '.self::$_root_directory.' && ' : '';
		if (strpos($command, 'https') !== FALSE) {
			$command = $cdCommand . $command . ' --username ' . self::$_account . ' --password ' . self::$_password . ' --no-auth-cache --trust-server-cert --non-interactive';
		} else {
			$command = $cdCommand . $command . ' --username ' . self::$_account . ' --password ' . self::$_password . ' --no-auth-cache --non-interactive';
		}
        return self::exec_cmd($command, true);
    }

    /**
     * @param $command
     * @return mixed
     */
    static public function exec_cmd($command, $need_output = false) {
        if ($need_output === true) {
            $command = $command . " 2>&1";
        }
        exec($command, $output, $status);
		self::$_last_query_cmd = $command;
		self::$_last_cmd_result = $output;
		self::$_last_cmd_status = $status;
        return $output;
    }
	
	static public function last_cmd() {
        return self::$_last_query_cmd;
    }

	static public function last_cmd_result() {
        return self::$_last_cmd_result;
    }

    static public function last_cmd_status() {
        return self::$_last_cmd_status;
    }

    /**
     * @param $string
     * @return bool|SimpleXMLElement
     */
    static function xml_parser($string) {
        $xml_parser = xml_parser_create();
        if (!xml_parse($xml_parser, $string, true)) {
            xml_parser_free($xml_parser);
            return false;
        } else {
            return new SimpleXMLElement($string);
        }
    }

	static function check_exec_status($exec_arr) {
		$fail = '--FAIL--';
		if (empty($exec_arr) || !is_array($exec_arr) || empty($exec_arr[0]))
			return FALSE;
		foreach ($exec_arr as $val) {
			if (strpos($val, $fail) !== FALSE)
				return FALSE;
		}
		if (end($exec_arr) == 'OK') {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}