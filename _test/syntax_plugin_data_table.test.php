<?php

/**
 * @group plugin_data
 * @group plugins
 */
class syntax_plugin_data_table_test extends DokuWikiTest {

    protected $pluginsEnabled = array('data', 'sqlite');

    private $exampleEntry = "---- datatable employees----\n"
            . "cols    : %pageid%, employees, deadline_dt, website_url, volume\n"
            . 'headers : Details, "Assigned Employees \#no", stuff outside quotes """Deadline, ",  Personal website, $$$'."\n"
            . "max     : 10\n"
            . "filter  : type=web development\n"
            . "sort    : ^(num)volume,%pageid%\n"
            . "dynfilters: 1\n"
            . "summarize : 1\n"
            . "align   : c\n"
            . "rownumbers: 1\n"
            . "widths  : 50px, 20em, - , 10%\n"
            . "----";

    function testHandle() {
        $plugin = new syntax_plugin_data_table();

        $handler = new Doku_Handler();
        $result = $plugin->handle($this->exampleEntry, 0, 10, $handler);

        $data = array(
            'classes' => 'employees',
            'limit' => 10,
            'dynfilters' => true,
            'summarize' => true,
            'rownumbers' => true,
            'sepbyheaders' => false,
            'headers' => array(
                '0' => 'Details',
                '1' => 'Assigned Employees #no',
                '2' => '"Deadline, ',
                '3' => 'Personal website',
                '4' => '$$$'
            ),
            'widths' => array(
                '0' => '50px',
                '1' => '20em',
                '2' => '-',
                '3' => '10%'
            ),
            'filter' => array(
                '0' => array(
                    'key' => 'type',
                    'value' => 'web development',
                    'compare' => '=',
                    'colname' => 'type',
                    'type' => '',
                    'logic' => 'AND'
                )
            ),
            'cols' => array(
                '%pageid%' => array(
                    'colname' => '%pageid%',
                    'multi' => '',
                    'key' => '%pageid%',
                    'origkey' => '%pageid%',
                    'title' => 'Title',
                    'type' => 'page',
                    'datatype' => ''
                ),
                'employee' => array(
                    'colname' => 'employees',
                    'multi' => 1,
                    'key' => 'employee',
                    'origkey' => 'employee',
                    'title' => 'employee',
                    'type' => '',
                    'datatype' => ''
                ),
                'deadline' => array(
                    'colname' => 'deadline_dt',
                    'multi' => '',
                    'key' => 'deadline',
                    'origkey' => 'deadline',
                    'title' => 'deadline',
                    'type' => 'dt',
                    'datatype' => ''
                ),
                'website' => array(
                    'colname' => 'website_url',
                    'multi' => '',
                    'key' => 'website',
                    'origkey' => 'website',
                    'title' => 'website',
                    'type' => 'url',
                    'datatype' => ''
                ),
                'volume' => array(
                    'colname' => 'volume',
                    'multi' => '',
                    'key' => 'volume',
                    'origkey' => 'volume',
                    'title' => 'volume',
                    'type' => '',
                    'datatype' => ''
                ),

            ),
            'sort' => array(
                'volume' => array('volume', 'DESC', 'numeric'),
                '%pageid%' => array('%pageid%', 'ASC', '')
            ),
            'align' => array(
                '0' => 'center'
            ),
            'sql' => "SELECT \" \" || pages.page, group_concat(\" \" || T1.value,'
'), group_concat(\" \" || T2.value,'
'), group_concat(\" \" || T3.value,'
'), group_concat(\" \" || T4.value,'
')
                FROM (
                    SELECT DISTINCT pages.pid AS pid
                    FROM pages  LEFT JOIN data AS T1 ON T1.pid = pages.pid AND T1.key = 'type'
                    WHERE 1 = 1 AND T1.value = 'web development'
                ) AS W1
                 LEFT JOIN data AS T1 ON T1.pid = W1.pid AND T1.key = 'employee' LEFT JOIN data AS T2 ON T2.pid = W1.pid AND T2.key = 'deadline' LEFT JOIN data AS T3 ON T3.pid = W1.pid AND T3.key = 'website' LEFT JOIN data AS T4 ON T4.pid = W1.pid AND T4.key = 'volume'
                LEFT JOIN pages ON W1.pid=pages.pid
                GROUP BY W1.pid
                ORDER BY CAST(T4.value AS NUMERIC) DESC, pages.page ASC LIMIT 11",
            'cur_param' => array()

);
        $this->assertEquals($data, $result, 'Data array corrupted');
    }

}


