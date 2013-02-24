<?php

class AlterC4p extends Ruckusing_Migration_Base
{
    public function up()
    {
        $this->add_column("c4p","session_order","integer");
    }//up()

    public function down()
    {
    }//down()
}
