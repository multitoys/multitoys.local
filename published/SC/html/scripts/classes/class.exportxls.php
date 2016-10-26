<?php
    /**
     * Created by PhpStorm.
     * User: Gololobov
     * Date: 10.11.2015
     * Time: 9:59
     */

    class ExportXLS
    {
        const  EXTENSION  = '.xls';
        
        public $_filename = 'report';
        public $rowNumber;
        public $colNumber;
        public $content;

        public function __construct($title = '')
        {
            $this->content = $this->_xlsBOF();

            if ($title) {
                $this->_fileTitle($title);
            }
        }

        protected function __destruct()
        {
            unlink($this->_filename.self::EXTENSION);
        }
        
        public function saveXLS()
        {
            $this->content .= $this->_xlsEOF();
            
            $filename = $this->_filename.self::EXTENSION;
            $handle = fopen($filename, 'a');
            fwrite($handle, $this->content);
            fclose($handle);

            if ($this->content) {

                header('Content-Type: application/vnd.ms-excel');
                header("Content-Disposition: attachment; filename=$filename");
                header("Content-Transfer-Encoding: binary ");
                readfile($filename);
            }
        }

        protected function _fileTitle($title)
        {
            $result = $this->_translitCyr($title);
            $result = strtolower(trim($result));
            $result = str_replace("'", '', $result);
            $result = preg_replace('#[^a-z0-9_]+#', '-', $result);
            $result = preg_replace('#\-{2,}#', '-', $result);
            $result = preg_replace('#(^\-+|\-+$)#D', '', $result);

            $this->_filename = $result;
        }

        protected function _xlsBOF()
        {
            return pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
        }

        protected function _xlsEOF()
        {
            return pack("ss", 0x0A, 0x00);
        }

        public function xlsWriteNumber($Value)
        {
            $Value = (is_string($Value))?0:$Value;
            return pack("sssss", 0x203, 14, $this->rowNumber, $this->colNumber, 0x0) . pack("d", (float)$Value);
        }

        public function xlsWriteLabel($Value)
        {
            $Value = $this->_utfToWin($Value);
            $length = strlen($Value);

            return pack("ssssss", 0x204, 8 + $length, $this->rowNumber, $this->colNumber, 0x0, $length).$Value;
        }

        protected function _utfToWin($string)
        {
            return iconv('UTF-8', 'WINDOWS-1251//IGNORE', $string);
        }

        protected function _translitCyr($cyr_str)
        {
            $tr = array("Ґ" => "G", "Ё" => "YO", "Є" => "E", "Ї" => "YI", "І" => "I",
                        "і" => "i", "ґ" => "g", "ё" => "yo", "№" => "_", "є" => "e",
                        "ї" => "yi", "А" => "A", "Б" => "B", "В" => "V", "Г" => "G",
                        "Д" => "D", "Е" => "E", "Ж" => "ZH", "З" => "Z", "И" => "I",
                        "Й" => "Y", "К" => "K", "Л" => "L", "М" => "M", "Н" => "N",
                        "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T",
                        "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "TS", "Ч" => "CH",
                        "Ш" => "SH", "Щ" => "SCH", "Ъ" => "'", "Ы" => "YI", "Ь" => "",
                        "Э" => "E", "Ю" => "YU", "Я" => "YA", "а" => "a", "б" => "b",
                        "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "zh",
                        "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l",
                        "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
                        "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h",
                        "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "",
                        "ы" => "yi", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya",
                        " " => "_", "," => "", "#" => "_"
            );

            return strtr($cyr_str, $tr);
        }
    }