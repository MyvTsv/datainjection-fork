<?php
/*
 ----------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2008 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org/
 ----------------------------------------------------------------------

 LICENSE

   This file is part of GLPI.

    GLPI is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    GLPI is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GLPI; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 ------------------------------------------------------------------------
*/

// Original Author of file: Walid Nouh
// Purpose of file:
// ----------------------------------------------------------------------

/*
 * Common backend to read files to import
 */
abstract class PluginDatainjectionBackend {

   private $file = "";
   protected $injectionDatas;
   private $delimiter;
   private $encoding;
   private $errmsg;

   const ENCODING_ISO8859_1 = 0;
   const ENCODING_UFT8      = 1;
   const ENCODING_AUTO      = 2;
   /*
    * Constructor
    * @param file input file to read
    */

   function getError($html=0)
   {
      return ($html ? nl2br($this->errmsg) : $this->errmsg);
   }

   function setError($msg)
   {
      if (!empty($this->errmsg))
         $this->errmsg .= "\n";
      $this->errmsg .= $msg;
   }
   function clearError()
   {
      $this->errmsg = "";
   }
   /*
    * Read datas from the input file
    */
   abstract protected function read();

   /*
    * Read n lines from the input files
    */
   abstract protected function readLinesFromTo($start_line, $end_line);

   /*
    * Get datas read from the input file
    * @return array with all the datas from the file
    */
   function getDatas()
   {
      return $this->injectionDatas->getDatas();
   }

   /*
    * Get header of the file
    * @return array with the datas from the header
    */
   function getHeader($header_present)
   {
      if ($header_present)
         return $this->injectionDatas->getDataAtLine(0);
      else
      {
         $nb = count($this->injectionDatas->getDataAtLine(0));
         for ($i=0; $i < $nb;$i++)
            $header[] = $i;

         return $header;
      }
   }

   /*
    * get datas from the file at line
    * @param line_id the id of the line
    * @return array with datas from this line
    */
   function getDataAtLine($line_id)
   {
      return $this->injectionDatas->getDataAtLine($line_id);
   }

   function getDatasFromLineToLine($start_line,$end_line)
   {
      $tmp = array();
      for ($i=$start_line;$i < $this->getNumberOfLine() && $i <= $end_line;$i++)
         $tmp[] = $this->injectionDatas->getDataAtLine($i);
      return $tmp;
   }

   abstract protected function isFileCorrect($model);

   function getNumberOfLine()
   {
      return count ($this->injectionDatas->getDatas());
   }

   abstract protected function deleteFile();

   abstract protected function export($file, $model, $tab_result);

   /*
    * Get the backend implementation by type
    */
   static function getInstance($type)
   {
      $class = 'PluginDatainjectionBackend'.$type;
      return new $class();
   }

   static function is_utf8($string) {

       // From http://w3.org/International/questions/qa-forms-utf-8.html
       return preg_match('%^(?:
             [\x09\x0A\x0D\x20-\x7E]            # ASCII
           | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
           |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
           | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
           |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
           |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
           | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
           |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
       )*$%xs', $string);
   }

   static function toUTF8($string)
   {
      if (!PluginDatainjectionBackend::is_utf8($string))
         return utf8_encode($string);
      else
         return $string;
   }
}

?>