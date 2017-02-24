<?php

namespace Myleshyson\LaravelQuickBooks\Services;

use Myleshyson\LaravelQuickBooks\Quickbooks;
use App\QuickBooks as QB;

class Connection extends Quickbooks
{
    /**
     * Connects to QuickBooks
     */
    //public function start()
    public function start($organization_id,$qb_consumer_key,$qb_consumer_secret,$oauth_url,$success_url)
    {
        //*************************************************************************************************
        $this->config = config('quickbooks');
        if (!\QuickBooks_Utilities::initialized($this->config['dsn'])) {
            \QuickBooks_Utilities::initialize($this->config['dsn']);
        }

        $this->IntuitAnywhere = new \QuickBooks_IPP_IntuitAnywhere($this->config['dsn'], $this->config['encryption_key'], $qb_consumer_key, $qb_consumer_secret, $oauth_url, $success_url);

        // Set up the IPP instance
        $IPP = new \QuickBooks_IPP($this->config['dsn']);
        if ($this->IntuitAnywhere->check($this->config['the_username'], $this->config['the_tenant']) and
            $this->IntuitAnywhere->test($this->config['the_username'], $this->config['the_tenant'])) {
            // Get our OAuth credentials from the database
            $creds = $this->IntuitAnywhere->load($this->config['the_username'], $this->config['the_tenant']);
            // Tell the framework to load some data from the OAuth store
            $IPP->authMode(
                \QuickBooks_IPP::AUTHMODE_OAUTH,
                $this->config['the_username'],
                $creds);


            $qbObj = QB::where("organization_id",'=',$organization_id)->first();
            $check_sandbox = ($qbObj->qb_company_id)?false:true;
            $IPP->sandbox($check_sandbox);

            $credentials = $qbObj->getFillable();
            foreach($credentials as $field){
                $qbObj[$field] = isset($creds[$field])?$creds[$field]:$qbObj[$field];
            }
            $qbObj["qb_sandbox_company_id"] = $creds["qb_realm"];
            $qbObj->save();
            // This is our current realm
            $this->realm = $creds['qb_realm'];
            // Load the OAuth information from the database
            $this->context = $IPP->context();
        }
        //***************************************************************************************
        $qb_id = QB::where("organization_id",'=',$organization_id)->first()->id;
        if ($this->IntuitAnywhere->handle($qb_id,$this->config['the_username'], $this->config['the_tenant'])) {
            ; // The user has been connected, and will be redirected to QBO_SUCCESS_URL automatically.
        } else {
            // If obj happens, something went wrong with the OAuth handshake
            die('Oh no, something went wrong with the Oauth handshake: ' . $this->IntuitAnywhere->errorNumber() . ': ' . $this->IntuitAnywhere->errorMessage());
        }
    }

    /**
     * Disconnects from QuickBooks
     */
    public function stop()
    {
        $this->IntuitAnywhere->disconnect($this->config['the_username'], $this->config['the_tenant'], true);
    }
    /**
     * Checks if Quickbooks is connected
     * @return boolean
     */
    public function check()
    {
        return $this->IntuitAnywhere->check($this->config['the_username'], $this->config['the_tenant']);
    }
}
