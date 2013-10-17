<?php

class Textpattern_Fluxbb_Api
{
    /**
     * Constructor.
     */

    public function __construct()
    {
        $_GET['type'] = 'xml';
        $_GET['action'] = 'feed';

        if (isset($_GET['sort']))
        {
            $_GET['order'] = $_GET['sort'];
        }

        if (isset($_GET['limit']))
        {
            $_GET['show'] = $_GET['limit'];
        }

        ob_start(array($this, 'outputJson'));
    }

    /**
     * Output JSON.
     *
     * @param string $buffer The output buffer
     */

    public function outputJson($buffer)
    {
        $xml = @simplexml_load_string($buffer);
        header('Content-Type: application/json; charset=UTF-8');

        if ($xml && $array = $this->xmlToArray($xml))
        {
            foreach ($array as &$value)
            {
                if (!is_array($value) || !isset($value[0]))
                {
                    $value = array($value);
                }
            }

            return json_encode($array);
        }
        else
        {
            if ($buffer)
            {
                header('Content-Type: application/json');
                header('HTTP/1.0 404 Not Found');
                return json_encode(array('errors' => (array) $buffer));
            }
        }

        return json_encode((array) $buffer);
    }

    /**
     * Converts XML to Array.
     *
     * @param mixed $xml The document
     */

    public function xmlToArray($xml)
    {
    	if (count($xml->children()) === 0)
        {
    		return (string) $xml;
    	}

    	$array = array();

        foreach ($xml->children() as $element => $node)
        {
    		$data = array();

            if (!$node->attributes())
            {
    			$data = $this->xmlToArray($node);
    		}
            else
            {
    			if (count($node->children()))
                {
    				$data = $data + $this->xmlToArray($node);
    			}
                else
                {
                    $data[] = (string) $node;
    			}

    		    foreach ($node->attributes() as $name => $value)
                {
                    $data[$name] = (string) $value;
    		    }
    		}

            if (count($xml->{$element}) > 1)
            {
                if (!isset($array[$element]))
                {
                    $array[$element] = array();
                }

                $array[$element][] = $data;
            }
            else
            {
                $array[$element] = $data;
            }
        }

    	return $array;
    }
}

new Textpattern_Fluxbb_Api();
include dirname(dirname(__FILE__)) . '/extern.php';
