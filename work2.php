<?php
/**
 * Created by PhpStorm.
 * User: hooklife
 * Date: 2018/10/25
 * Time: 4:26
 */


class Table{
    public function getuser()
    {
        return "ok";
    }


    public function test()
    {
        $field = 'user';
        echo "class find\r\n";

        $t1 = microtime(true);
        foreach (range(1,500) as $a){
            $func = "get".$field;
            if (method_exists($this,$func)) {
                call_user_func([$this,$func]);
            }
        }

        var_dump(microtime(true)-$t1);



        echo "array find\r\n";
        $t1 = microtime(true);
        foreach (range(1,500) as $a) {
            $a = ['set.user' => 'getuser', 'get.user' => 'getuser'];
            $func = 'set.' . $field;
//            if (isset($a[$func])) {
                call_user_func([$this, $a[$func]]);
//            }
        }
        var_dump(microtime(true)-$t1);
    }
}




(new Table())->test();







