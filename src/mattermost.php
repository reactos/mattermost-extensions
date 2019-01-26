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
        $script_dir = dirname(__FILE__);

        $this->post_arg = $post ?? $_POST;
        $this->get_arg = $get ?? $_GET;
        $this->arg = trim($this->post('text'));
        $this->token = $this->post('token');
        $this->user = $this->post('user_name');
        $this->config = require($script_dir . '/config.php');
        $this->result['response_type'] = 'ephemeral';
        $this->result['text'] = '<No output>';
    }

    function get($name)
    {
        return isset($this->get_arg[$name]) ? $this->get_arg[$name] : '';
    }

    function post($name)
    {
        return isset($this->post_arg[$name]) ? $this->post_arg[$name] : '';
    }

    function validate()
    {
        $outer_name = strtolower(get_class($this));
        $expect_token = $this->config[$outer_name . '_token'];
        if (!is_array($expect_token))
            $expect_token = array($expect_token);
        if (!in_array($this->token, $expect_token))
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
