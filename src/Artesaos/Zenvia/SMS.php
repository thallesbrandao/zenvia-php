<?php
namespace Artesaos\Zenvia;

use Artesaos\Zenvia\Contracts\AuthenticatorInterface;
use Artesaos\Zenvia\Contracts\RequestManagerInterface;
use Artesaos\Zenvia\Contracts\ResponseHandlerInterface;
use Artesaos\Zenvia\Contracts\SMSInterface;
use Artesaos\Zenvia\Exceptions\ZenviaContractException;
use Artesaos\Zenvia\Http\RequestManager;
use Artesaos\Zenvia\Http\ResponseHandler;

class SMS implements SMSInterface {


    /**
     * @var RequestManagerInterface
     */
    private $requestManager;
    /**
     * @var AuthenticatorInterface
     */
    private $authenticator;

    /**
     * SMS constructor.
     *
     * @param string $account
     * @param string $password
     */
    public function __construct($account, $password)
    {
        $this->requestManager = new RequestManager();
        $this->authenticator = new Authenticator($account, $password);
    }
    
    /**
     * {@inheritdoc}
     */
    public function send(array $body,$aggregateId = null, $responseFormat = 'psr7')
    {
        $data['sendSmsRequest'] = $body;

        if($aggregateId != null){
            $data['sendSmsRequest']['aggregateId'] = $aggregateId;
        }
        $response = $this->getRequestManager()
                           ->sendRequest('POST','services/send-sms',$data, $this->authenticator->getAccessCode(),'1.1');

        return ResponseHandler::convert($response,$responseFormat);

    }

    /**
     * {@inheritdoc}
     */
    public function sendMultiple(array $data,$aggregateId = null, $responseFormat = 'psr7')
    {
        if($aggregateId != null){
            $array_data['sendSmsMultiRequest']['aggregateId'] = $aggregateId;
        }

        $array_data['sendSmsMultiRequest']['sendSmsRequestList'] = $data;
        $response = $this->getRequestManager()
            ->sendRequest('POST','services/send-sms-multiple',$array_data, $this->authenticator->getAccessCode(),'1.1');

        return ResponseHandler::convert($response,$responseFormat);

    }

    /**
     * @return RequestManagerInterface
     */
    public function getRequestManager()
    {
        return $this->requestManager;
    }

    /**
     * @param RequestManagerInterface $requestManager
     */
    public function setRequestManager(RequestManagerInterface $requestManager)
    {
        $this->requestManager = $requestManager;
    }

    /**
     * @param AuthenticatorInterface $authenticator
     * @return $this
     */
    public function setAuthenticator(AuthenticatorInterface $authenticator)
    {
        $this->authenticator = $authenticator;
        return $this;
    }

    /**
     * @return AuthenticatorInterface
     */
    public function getAuthenticator()
    {
       return $this->authenticator;
    }
}