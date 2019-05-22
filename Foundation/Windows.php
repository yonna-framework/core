<?php

namespace {

    class Windows
    {

        /**
         * 播放声音
         * @param $str
         */
        public function sound($str)
        {
            if (Is::windows()) @exec('mshta vbscript:createobject("sapi.spvoice").speak("' . $str . '")(window.close)');
        }
    }

}