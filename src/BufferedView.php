<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 1/24/14
 * @time 10:09 AM
 */

namespace Jabberd2;


class BufferedView extends View
{
    /**
     * @return mixed
     */
    public function run()
    {
        @ob_end_flush();
        ob_start();
        parent::run();
        return ob_get_clean();
    }

} 