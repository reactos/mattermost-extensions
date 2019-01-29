<?php

$script_dir = dirname(__FILE__);
require_once($script_dir . '/mattermost.php');

class Git extends Mattermost
{
    function validate()
    {
        // Validate the token
        if (!parent::validate())
            return false;

        // Do our own validation
        if (!preg_match('/^[g]?([a-f0-9]{7,})$/i', $this->arg, $matches))
        {
            $this->result['text'] = 'Invalid commit hash';
            return false;
        }
        $this->hash = $matches[1];
        return true;
    }

    function process($user, $arg)
    {
        $this->result['text'] = "@$user requested `/git $arg`:\n" .
            "https://github.com/reactos/reactos/commit/$this->hash\n" .
            "https://git.reactos.org/?p=reactos.git;a=commit;h=$this->hash";
        $this->result['response_type'] = "in_channel";
    }
}

(new Git)->run();

