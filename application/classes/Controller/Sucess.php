
<?php defined('SYSPATH') OR die('No Direct Script Access');
 
Class Controller_Sucess extends Controller_Template
{
    public $template = 'login1';
 
    public function action_index()
    {
        $this->template->message = 'hello, world!';
    }
}