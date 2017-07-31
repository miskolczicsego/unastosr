<?php
namespace ShopMoves\UnasMigrationBundle\Api;
use ShopMoves\UnasMigrationBundle\Curl\Curl;
use ShopMoves\UnasMigrationBundle\Config\Config;
use Symfony\Component\Security\Acl\Exception\Exception;


/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.06.29.
 * Time: 13:41
 */
class ApiCall
{
    /**
     * @var Curl $curl
     */
    protected $curl;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $format = 'json';

    protected $response;


    /**
     * ApiCall constructor.
     * @param Curl $curl
     * @param Config $config )
     */
    public function __construct(Curl $curl, Config $config)
    {
        $this->curl = $curl;

        $this->setConfig($config);
    }

    /**
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->apiKey = $config->getApiKey();
        $this->username = $config->getUserName();
        $this->url = $config->getUrl();
    }

    /**
     * @param string $uri
     * @return Response
     */
    public function executeGet($uri)
    {
        return $this->doExecute($uri);
    }

    /**
     * @param $curlHandle
     */
    protected function setOptions($curlHandle)
    {
        curl_setopt($curlHandle, CURLOPT_HEADER, 1);
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curlHandle, CURLOPT_MAXREDIRS, 5);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array("Expect:"));
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array("Accept: application/" . $this->format));
    }

    /**
     * @param $curlHandle
     */
    protected function setAuth($curlHandle)
    {
        curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curlHandle, CURLOPT_USERPWD, $this->username . ':' . $this->apiKey);
    }

    protected function setUrl($curlHandle, $url)
    {
        curl_setopt($curlHandle, CURLOPT_URL, $url);
    }

    public function execute($method, $uri, $data)
    {
        $curlHandle = curl_init();
        $this->setUrl($curlHandle, $this->url . $uri);
        $this->setAuth($curlHandle);
        $this->setOptions($curlHandle);

        switch ($method) {
            case 'GET':
                $this->executeGet($curlHandle);
                break;
            case 'POST':
                $this->executePost($curlHandle, $data);
                break;
            case 'PUT':
                $this->executePut($curlHandle, $data);
                break;
            case 'DELETE':
                $this->executeDelete($curlHandle);
                break;
            default:
                throw new Exception('Invalid HTTP METHOD');
        }

        return $this->response;
    }

    /**
     * @param string $uri
     * @param array $data
     * @return Response
     */
    public function executePost($curlHandle, array $data)
    {
        $postFields = array();
        $this->processLevel($postFields, array('data' => $data));
        curl_setopt($curlHandle, CURLOPT_POST, true);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $postFields);

        $this->doExecute($curlHandle);
    }

    /**
     * @param string $uri
     * @param array $data
     * @return Response
     */
    public function executePut($uri, array $data)
    {
        $this->curl->setOption(CURLOPT_CUSTOMREQUEST, "PUT");
        $this->curl->setOption(CURLOPT_POSTFIELDS, $data);

        return $this->doExecute($uri);
    }

    /**
     * @param string $uri
     * @param array $data
     * @return Response
     */
    public function executePatch($uri, array $data)
    {
        $this->curl->setOption(CURLOPT_CUSTOMREQUEST, "PATCH");
        $this->curl->setOption(CURLOPT_POSTFIELDS, $data);

        return $this->doExecute($uri);
    }

    /**
     * @param string $uri
     * @return Response
     */
    public function executeDelete($uri)
    {
        $this->curl->setOption(CURLOPT_CUSTOMREQUEST, "DELETE");

        return $this->doExecute($uri);
    }

    /**
     * @param string $uri
     * @return Response
     */
    protected function doExecute($curlHandle)
    {
        ob_start();
        curl_exec($curlHandle);
        $content = ob_get_contents();
        ob_end_clean();

        $headerSize = curl_getinfo($curlHandle, CURLINFO_HEADER_SIZE);

        $headers = substr($content, 0, $headerSize);
        $responseBody = substr($content, $headerSize);

        $statusCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($curlHandle, CURLINFO_CONTENT_TYPE);

        preg_match("!\r\n(?:Location|URI): *(.*?) *\r\n!", $headers, $matches);
        $this->response = new Response($responseBody, $contentType, $statusCode);
        curl_close($curlHandle);
    }

    /**
     * @param array $result
     * @param array $source
     * @param null $previousKey
     */
    protected function processLevel(array &$result, array $source, $previousKey = null)
    {
        foreach ($source as $k => $value) {
            $key = $previousKey ? "{$previousKey}[{$k}]" : $k;
            if (!is_array($value)) {
                $result[$key] = $value;
            } else {
                $this->processLevel($result, $value, $key);
            }
        }
    }
}