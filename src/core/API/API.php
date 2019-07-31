<?php


namespace API;

use Validator\Validator;

/**
 * Class API
 * @package API
 * API handles common low-level API operations,
 * and defines hooks for higher level API to
 */
abstract class API
{
    const RESPONSE_TYPE_HTML = 'text/html';
    const RESPONSE_TYPE_JSON = 'text/json';

    const FIELD_TYPE_ID = 'integer';
    const FIELD_TYPE_STRING = 'string';
    const FIELD_TYPE_DATE = 'date';

    const SEARCH_TYPE_EQ = '=';
    const SEARCH_TYPE_LT = '<';
    const SEARCH_TYPE_FE = '<=';
    const SEARCH_TYPE_GT = '>';
    const SEARCH_TYPE_GE = '>=';
    const SEARCH_TYPE_STARTS_WITH = 'STAR_LIKE';
    const SEARCH_TYPE_ENDS_WITH = 'LIKE_STAR';


    private $http_code;
    private $error_text;
    protected $responseType;
    protected $container;
    protected $name;

    public function __construct()
    {
        $this->container = [];
        $this->container['data'] = [];
        $this->container['meta'] = $this->getAPIMeta();
        $this->name = get_called_class();
        $this->responseType = self::RESPONSE_TYPE_HTML;

    }

    public function processRequest()
    {
        if ($this->isGet()) {
            if ($this->doGet()) {
                $this->setHttpError(200, 'Request Successful');
            }
        } elseif ($this->isPost()) {
            if ($this->doPost()) {
                $this->setHttpError(200, 'Request Successful');
            }
        }
    }

    public function output()
    {
        if ($this->http_code === 200) {
            switch ($this->responseType) {
                case self::RESPONSE_TYPE_JSON:
                    $this->outputJSON();
                    break;
                case self::RESPONSE_TYPE_HTML:
                    $this->outputHTML();
                    break;
                default:
                    error_log('unknown response type: ' . $this->responseType);
                    break;
            }
        } else {
            echo json_encode(['error-code' => $this->http_code, 'message' => $this->error_text]);
        }
        return;
    }

    protected function getRequestAsFields()
    {
        $fields = [];
        $meta = $this->getAPIMeta();

        foreach ($meta as $field => $definition) {
            if (isset($_REQUEST[$field])) {
                $value = trim($_REQUEST[$field]);
                if ($value && $value !== '') {
                    if ($definition['type'] === self::FIELD_TYPE_ID) {
                        $value = intval($value);
                    }
                    $fields[$field] = $value;
                }
            }
        }

        return $fields;
    }

    protected abstract function getEndPointList();

    protected function isGet()
    {
        return isset($_GET);
    }

    protected function isPost()
    {
        return isset($_POST);
    }

    protected function getAPIMeta()
    {
        return [];
    }


    protected function doGet()
    {
        return true;
    }

    protected function doPost()
    {
        return true;
    }

    protected function doPut()
    {
        return true;
    }

    protected function doDelete()
    {

    }

    protected function setHttpError($code, $text)
    {
        $this->http_code = $code;
        $this->error_text = $text;
    }

    protected function validateRequestFields(array $fields, array $meta)
    {
        $baseAPIFields = $this->getAPIMetaFields();
        foreach ($baseAPIFields as $key => $baseAPIField) {
            $meta[$key] = $baseAPIField;
        }
        if (empty($fields)) {
            return true;
        }
        //only allow defined fields
        $allowedFields = array_keys($meta);
        $presentFields = array_keys($fields);
        $difference = array_diff($presentFields, $allowedFields);
        if (!empty($difference)) {
            $this->setHttpError(400, 'Invalid Fields');
            return false;
        }

        foreach ($meta as $field => $fieldMeta) {
            switch ($fieldMeta['type']) {
                case self::FIELD_TYPE_ID:
                    if (isset($fields[$field]) && !Validator::isInteger($fields[$field])) {
                        $this->setHttpError(400, 'Invalid Value for field: ' . $field);
                        return false;
                    }
                    break;
                case self::FIELD_TYPE_STRING:
                    if (isset($fieldMeta['maxlen'])) {
                        if (isset($fields[$field]) && !Validator::maxLength($fields[$field], $fieldMeta['maxlen'])) {
                            $this->setHttpError(400, 'Invalid Length for field: ' . $field);
                            return false;
                        }
                    } elseif (isset($fieldMeta['alnum'])) {
                        if (isset($fields[$field]) && !Validator::isAlnum($fields[$field])) {
                            $this->setHttpError(400, 'Invalid Value for field: ' . $field);
                            false;
                        }
                    }
                    break;
                case self::FIELD_TYPE_DATE:
                    if (isset($fields[$field]) && !Validator::isIsoDate($fields[$field])) {
                        $this->setHttpError(400, 'Invalid Value for field: ' . $field);
                    }
                    break;
            }
        }
        return true;
    }

    protected function translateApiToDBType($apiType)
    {
        switch ($apiType) {
            case self::FIELD_TYPE_ID:
                return 'i';
                break;
            case self::FIELD_TYPE_DATE:
            case self::FIELD_TYPE_STRING:
                return 's';
                break;
            default:
                throw new \Exception('Unsupported Type');
        }
    }

    private function getAPIMetaFields()
    {
        return ['_accept-type' => ['type' => self::FIELD_TYPE_STRING, 'label' => 'Accept Type', 'maxlen' => 9]];
    }

    private function outputHTML()
    {
        $json = json_encode($this->container);

        $htmlContainer = $this->htmlEncodeContainer($this->container);

        $meta = $this->getAPIMeta();
        echo <<<END
        <html>
           <head>
              <title>$this->name</title>
           </head>
           <body>
               <h2>Request</h2>
               <h3>GET</h3>
               <form action="" method="get">
               <ul>
END;
        foreach ($meta as $key => $apiMeta) {
            printf('<li> %s <input type="text" id="%s" name="%s" /></li>' . PHP_EOL, $apiMeta['label'], $key, $key);
        }
        if (count($meta)) {
            echo 'HTML <input type="radio" id="_accept-type" name="_accept-type" value="text/html" checked="checked" /><br/>' .
                'JSON <input type="radio" id="_accept-type" name="_accept-type" value="text/json" /><br/>' .
                '<input type="submit" value="Submit" />';
        }
        echo <<<END
               </ul>
               </form>
              <h2>Result JSON</h2>
              <pre>
                $json
              </pre> 
              <h2>Result HTML</h2>
              <ul>
                $htmlContainer
              </ul>
           </body>
       </html>
END;

    }

    private function htmlEncodeContainer($content)
    {
        $prefix = '<ul>';
        foreach ($content as $key => $value) {
            error_log('key: ' . $key . ' value: ' . var_export($value, true));
            if ('links' == $key) {
                $prefix .= '<h3>Links</h3><ul>' . $this->htmlEncodeLinks($value) . '</ul>';
            } elseif ('meta' === $key) {
                $prefix .= '<h3>Meta</h3><ul>' . $this->htmlEncodeMeta($value) . '</ul>';
            } elseif (is_array($value)) {
                $prefix .= '<h3>' . $key . '</h3><li><ul>' . $this->htmlEncodeEntity($value) . '</ul></li>';
            } else {
                $prefix .= '<li>' . $key . ':' . $value . '</li>';
            }
        }
        $prefix .= '</ul>';
        return $prefix;
    }

    private function htmlEncodeLinks($links)
    {
        $prefix = '';
        foreach ($links as $target => $name) {
            $prefix .= '<li><a href="' . $target . '">' . $name . '</a></li>';
        }
        return $prefix;
    }

    private function htmlEncodeEntity($elist)
    {
        $prefix = '';
        if ($elist) {
            foreach ($elist as $key => $value) {
                $prefix .= '<li><h3>' . $key . '</h3><ul>';
                foreach ($value as $field => $result) {
                    $prefix .= '<li>' . $field . ':' . $result . '</li>';
                }
                $prefix .= '</ul></li>';
            }
        }
        return $prefix;
    }

    private function htmlEncodeMeta($meta)
    {
        $prefix = '';
        foreach ($meta as $key => $metaInfo) {
            $prefix .= '<li><h3>' . $key . '</h3><ul><li>label: ' . $metaInfo['label'] . '</li><li>type: ' . $metaInfo['type'] . '</li></ul>';
        }
        return $prefix;
    }

    private function outputJSON()
    {
        echo json_encode($this->container);
    }
}