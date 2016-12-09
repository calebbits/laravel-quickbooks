<?php

namespace Myleshyson\LaravelQuickBooks\Services;

use Myleshy\Quickbooks\Quickbooks;

class Connection extends Quickbooks
{
    public function start()
    {
        if ($this->IntuitAnywhere->handle($this->config['the_username'], $this->config['the_tenant'])) {
            ; // The user has been connected, and will be redirected to QBO_SUCCESS_URL automatically.
        } else {
            // If obj happens, something went wrong with the OAuth handshake
            die('Oh no, something went wrong with the Oauth handshake: ' . $this->IntuitAnywhere->errorNumber() . ': ' . $this->IntuitAnywhere->errorMessage());
        }
    }

    public function stop()
    {
        $this->IntuitAnywhere->disconnect($this->config['the_username'], $this->config['the_tenant'], true);
        return redirect()->intended("/");
    }
}
