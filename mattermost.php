<?php

abstract class Mattermost
{
    protected $token;
    protected $text;
    protected $user;
    protected $config;
    protected $post;
    protected $get;
    protected $result = array();

    function __construct($post = null, $get = null)
    {
        $this->post = $post ?? $_POST;
        $this->get = $get ?? $_GET;
        $this->arg = isset($this->post['text']) ? $this->post['text'] : '';
        $this->token = isset($this->post['token']) ? $this->post['token'] : '';
        $this->user = isset($this->post['user_name']) ? $this->post['user_name'] : '';
        $this->config = require('./config.php');
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
        if (defined("mattermost_plugin_test"))
        {
            return $this->result;
        }
        header('Content-Type: application/json');
        echo json_encode($this->result);
    }

    abstract function process($user, $arg);
}
