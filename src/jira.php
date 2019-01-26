<?php

$script_dir = dirname(__FILE__);
require_once($script_dir . '/mattermost.php');

class Jira extends Mattermost
{
    function validate()
    {
        // Validate the token
        if (!parent::validate())
            return false;

        // Do our own validation
        if (!preg_match('/^(ARWINSS|CORE|ROSBE|ONLINE|ROSTESTS|ROSAPPS)-[0-9]+$/i', $this->arg))
        {
            $this->result['text'] = 'Invalid issue ID';
            return false;
        }
        return true;
    }

    function process($user, $arg)
    {
        $this->result['text'] = "Url requested by $user: https://jira.reactos.org/browse/$arg";
        $this->result['response_type'] = "in_channel";
    }
}

(new Jira)->run();

