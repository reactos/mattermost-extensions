<?php

$script_dir = dirname(__FILE__);
require_once($script_dir . '/mattermost.php');

class Pr extends Mattermost
{
    static $repos = array(
        'reactos',
        'rapps-db',
        'sysreg2',
        'buildbot_config',
        'rosev_ircsystem',
        'web',
        'RosBE',
        'Release_Engineering',
        'press-media',
        'git-tools',
        'rosev_jameicaplugin',
        'wine',
        'Message_Translator',
        'vmwaregateway',
        'RosTE',
        'reactosdbg',
        'Qemu_GUI',
        'monitoring',
        'irc',
        'ahk_tests',
        'documentation',
        'ccache',
    );

    function validate()
    {
        // Validate the token
        if (!parent::validate())
            return false;

        // Do our own validation
        if (!preg_match('/^([a-z0-9-_]*)#?([0-9]+)$/i', $this->arg, $matches))
        {
            $this->result['text'] = 'Invalid PR number';
            return false;
        }
        $repo = array_search(strtolower($matches[1]), array_map('strtolower',Pr::$repos));
        if ($repo < 0)
            $repo = 0;
        $this->repo = Pr::$repos[$repo];
        $this->num = $matches[2];
        return true;
    }

    function process($user, $arg)
    {
        $this->result['text'] = "@$user requested `/pr $arg`:\n" .
            "https://github.com/reactos/$this->repo/pull/$this->num";
        $this->result['response_type'] = "in_channel";
    }
}

(new Pr)->run();

