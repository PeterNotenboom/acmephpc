<?php

namespace Octopuce\Acme\ChallengeSolver;

/**
 * Dns Challenge solver for DVSNI
 */
class Dns implements SolverInterface
{
    /**
     * TXT Dns record
     */
    private $dns_record = '';

    /**
     * TXT Dns record value based on a binary sha256
     */
    private $authkeyhook = '';

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
    }

    /**
     * Solve the challenge by creating a TXT record _acme-challenge.domain.com with the correct sha256 hook based on the token + thumbprint
     *
     * @param string $token
     * @param string $key
     *
     * @return bool
     *
     * @throws \RuntimeException
     */
    public function solve($token, $key)
    {
        $solved = false;
        $txt_records = dns_get_record($this->dns_record, DNS_TXT);
        foreach($txt_records AS $txt_record) {
          if($txt_record['host'] == $this->dns_record) {
            //Found an acme TXT record
            //Now see if it's actually this value
            if($txt_record['txt'] == $this->authkeyhook) {
              $solved = true;
            }
          }
        }
        if($solved === false) {
          throw new \RuntimeException(sprintf('Unable to verify DNS record %s with the value %s', $this->dns_record, $this->authkeyhook));
        }
        return true;
    }

    /**
     * Solve the challenge by creating a TXT record _acme-challenge.domain.com with the correct sha256 hook based on the token + thumbprint
     *
     * @param string $fqdn
     * @param string $token
     * @param string $thumbprint
     *
     * @return array
     *
     * @throws \RuntimeException
     */
    public function getChallengeInfo($fqdn, $token, $thumbprint)
    {
      $this->dns_record = '_acme-challenge.'.$fqdn;
      $this->authkeyhook = base64_encode(openssl_digest($token.'.'.$thumbprint, 'sha256', true));
      // urlbase64: base64 encoded string with '+' replaced with '-' and '/' replaced with '_'
      $this->authkeyhook = str_replace(array('+', '/'), array('-', '_'), $this->authkeyhook);

      return array(
          'info' => sprintf('Create a TXT record %s with the value %s', $this->dns_record.'.', $this->authkeyhook),
          'keyAuthorization' => $token.'.'.$thumbprint,
      );
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return 'dns-01';
    }
}
