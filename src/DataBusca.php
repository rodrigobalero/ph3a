<?php

namespace Ph3a;

class DataBusca{
    
    private $client;

    private $baseUrl;

    private $domainId;
    private $userName;
    private $password;

    private $token;

    private $endDate;

    /**
    *   @param GuzzleHttpObject $httpClient     Guzzle HTTP Object
    *   @param Array $parmas        Login and URL params
    **/
    public function __construct($httpClient, $params = array()){
        
        $this->domainId = $params["domainid"];

        $this->userName = $params["username"];

        $this->password = $params["password"];

        $this->baseUrl = $params["apiUrl"];
        
        $this->client = $httpClient;

    }

    /**
    *   Verify if the token still is valid, if not invokes the login method again.
    **/
    private function verifyToken(){

        if(time() >= $this->endDate && is_null($this->endDate)){
            $this->login();
        }

        return true;
    }

    /**
    *   Do the request on PH3A API and returns the json body
    *   @param String $methodParam
    **/
    private function request($methodParam, $postBody){

        $headers = ['headers' => [
            'Content-Type'     => 'application/json',
        ]];
        
        $request = $this->client
            ->request('POST', 
            $this->baseUrl.$methodParam,
            ['json'=>($postBody),'debug' => false, $headers, 'connect_timeout' => 120]);

        $body = $request->getBody();

        $bodyContents = json_decode($body->getContents(),true);

        return $bodyContents;
    }

    public function login(){

        $methodParam = "/Login";

        $postBody = ["DomainId"=>$this->domainId,
                    "UserName"=>$this->userName,
                    "Password"=>$this->password];

        
        $requestResponse = $this->request($methodParam, $postBody);

        $this->token = $requestResponse["Token"];

        $endDate = $requestResponse["EndDate"];
        $endDate = explode("Date(",$endDate);
        $endDate = explode("-",$endDate[1]);
        $this->endDate = $endDate[0];

        return $this;
    }


    public function getCompanyData($documentNumber){

        $this->verifyToken();

        $methodParam = "/GetCompanyData";

        $postBody = ["Token"=>$this->token,
                    "Document"=>$documentNumber];

        $requestResponse = $this->request($methodParam, $postBody);


        return $requestResponse;

    }

    public function getPersonData($documentNumber){

        $this->verifyToken();

        $methodParam = "/GetPersonData";

        $postBody = ["Token"=>$this->token,
                    "Document"=>$documentNumber];

        $requestResponse = $this->request($methodParam, $postBody);

        return $requestResponse;

    }

    public function getOwnerPhone($areaCode, $phone){

        $this->verifyToken();

        $methodParam = "/GetOwnerPhone";

        $postBody = ["Token" => $this->token,
                    "AreaCode" => $areaCode,
                    "PhoneNumber" => $phone
                    ];

        $requestResponse = $this->request($methodParam, $postBody);

        return $requestResponse;
    }

    public function getCompanySituacaoCadastral($documentNumber, $serpro = false){

        $this->verifyToken();

        $methodParam = "/GetCompanySituacaoCadastral";

        $postBody = ["Token" => $this->token,
                    "Document" => $documentNumber,
                    "BirthDate" => NULL,
                    "ApiSerpro" => $serpro
                    ];

        $requestResponse = $this->request($methodParam, $postBody);

        return $requestResponse;

    }

    public function GetPersonByParameters($documentNumber, $areaCode, $phoneNumber, $birthDate, $zipCode, $email){

        $this->verifyToken();

        $methodParam = "/GetPersonByParameters";

        $postBody = ["Token" => $this->token,
                    "Name" => $documentNumber,
                    "AreaCode" => $areaCode,
                    "PhoneNumber" => $phoneNumber,
                    "BirthDate" => $birthDate,
                    "ZipCode" => $zipCode,
                    "Email" => $email,
                    ];

        $requestResponse = $this->request($methodParam, $postBody);

        return $requestResponse;

    }

    public function getPersonSituacaoCadastral($documentNumber, $birthDate, $serpro = false){

        $this->verifyToken();

        $methodParam = "/GetPersonSituacaoCadastral";

        $postBody = ["Token" => $this->token,
                    "Document" => $documentNumber,
                    "BirthDate" => $birthDate,
                    "ApiSerpro" => $serpro
                    ];

        $requestResponse = $this->request($methodParam, $postBody);

        return $requestResponse;

    }

}

