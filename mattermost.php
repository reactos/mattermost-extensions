<?php

abstract class Mattermost
{
    protected $token;
    protected $text;
    protected $user;
    protected $config;
    protected $result = array();

    function __construct()
    {
        $this->config = include_once('config.php');
        $this->arg = isset($_POST['text']) ? $_POST['text'] : '';
        $this->token = isset($_POST['token']) ? $_POST['token'] : '';
        $this->user = isset($_POST['user_name']) ? $_POST['user_name'] : '';
        $this->result['response_type'] = 'ephemeral';
        $this->result['text'] = '<No output>';
    }

    function validate()
    {
        $outer_name = strtolower(get_class($this));
        $expect_token = $this->config[$outer_name . '_token'];
        if ($this->token != $expect_token)
        {
            $this->result['text'] = 'Invalid token';
            return false;
        }
        return true;
    }

    function run()
    {
        if ($this->validate())
        {
            $this->process($this->user, $this->arg);
        }
        $this->output();
    }

    abstract function process($user, $arg);

    function output()
    {
        header('Content-Type: application/json');
        echo json_encode($this->result);
    }
}

?>
