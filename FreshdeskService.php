<?php

define("TICKET_CTI_EP",    "/api/v2/integrations/cti/pop");
define("CONTATO_EP",       "/api/v2/contacts");
define("AGENTE_EP",        "/api/v2/agents");


class FreshdeskService{

        private $host;
        private $apiKey;

    function FreshdeskService($apiKey, $host){
        $this->host = $host;
        $this->apiKey = $apiKey;
    }

    function getHost(){ 
        return $this->host; 
    }
    
    function getApiKey(){ 
        return $this->apiKey; 
    }
    
    function listContatoByPhone($phone){
        $host = $this-getHost().CONTATO_EP."?phone=".$phone;
        return $this->requestGet($host);
    }

    function listAgentByPhone($phone){
        $host = $this-getHost().AGENTE_EP."?phone=".$phone;
        return $this->requestGet($host);
    }

    function novoTicketCti($requester_id, $responder_id, $call_id, $responder_email){
        $host = $this-getHost().TICKET_CTI_EP;
        $data = array(
            "requester_id"      => $requester_id,
            "responder_id"      => $responder_id,
            "call+reference_id" => $call_id,
            "responder_email"   => $responder_email,
            "new_ticket"        => true
        );
        return $this->requestPost($host, $data);
    }

    function novoContato($email, $phone){
        $host = $this-getHost().CONTATO_EP;
        $data = array(
            "email "      => $email,
            "phone "      => $phone
        );
        return $this->requestPost($host, $data);
    }

    private function requestGet($host){
        $ch = $this->initCurl($host);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        return $this->execCurl($ch);
    }

    private function requestPost($host, $arrayData){
        $ch = $this->initCurl($host);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayData)); 
        curl_setopt($ch, CURLOPT_POST, 1);
        return $this->execCurl($ch);
    }

    private function initCurl($host){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD, $this->buildAuth());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->buildHeader());
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        return $ch;
    }

    private function execCurl($ch){
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Erro: ' . curl_error($ch);
        }
        curl_close ($ch);
        return $result;
    }

    private function buildHeader(){
        $headers = array();
        $headers[] = "Content-Type: application/json";
        return $headers;
    }

    private function buildAuth(){
        return $this->getApiKey().":X";
    }
    
}