<?php

$script_dir = dirname(__FILE__);
require_once($script_dir . '/mattermost.php');

class Translate extends Mattermost
{
    static $xml_info = array(
        'wm' => array('query' => '//WindowMessageList/WindowMessage[@value="%u"]'),
        'hresult' => array('query' => '//HresultList/Hresult[@value="%08X"]'),
        'ntstatus' => array('query' => '//NtstatusList/Ntstatus[@value="%08X"]'),
        'winerror' => array('query' => '//WinerrorList/Winerror[@value="%u"]'),
    );
    static $xml_lookup = array(
        'wm' => array('wm'),
        'error' => array('winerror', 'ntstatus', 'hresult')
    );
    function validate()
    {
        // Validate the token
        if (!parent::validate())
            return false;

        // Do we know this command?
        $this->cmd = $this->get('cmd');
        if (!array_key_exists($this->cmd, Translate::$xml_lookup))
        {
            $this->result['text'] = 'Invalid command';
            return false;
        }

        // Validate the numeric input
        $value = filter_var($this->arg, FILTER_VALIDATE_INT, FILTER_FLAG_ALLOW_HEX);
        if ($value === false)
        {
            $this->result['text'] = 'Not a number';
            return false;
        }

        $this->value = $value;
        return true;
    }

    function xml_entries($name)
    {
        global $script_dir;
        $xml = simplexml_load_file($script_dir . "/data/$name.xml");
        $fmt = Translate::$xml_info[$name]['query'];
        $entries = array();
        foreach($xml->xpath(sprintf($fmt, $this->value)) as $entry)
        {
            $entries[] = $entry['text']->__toString();
        }
        return $entries;
    }

    function process($user, $arg)
    {
        $results = array();
        foreach(Translate::$xml_lookup[$this->cmd] as $name)
        {
            $results = array_merge($results, $this->xml_entries($name));
        }
        if (!empty($results))
        {
            $combined = "@$user requested `/$this->cmd $arg`:";
            foreach($results as $result)
            {
                $combined .= "\n\t$result";
            }
            $this->result['text'] = $combined;
            $this->result['response_type'] = "in_channel";
        }
        else
        {
            $this->result['text'] = "$arg not found";
        }
    }
}

(new Translate)->run();

