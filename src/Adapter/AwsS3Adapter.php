<?php

namespace Plumpboy\Filemanager\Adapter;

use League\Flysystem\AwsS3v3\AwsS3Adapter as LeagueAwsS3Adapter;
use Aws\CloudFront\CloudFrontClient;

class AwsS3Adapter extends LeagueAwsS3Adapter
{
    protected $cloudFront;
    protected $config;

    public function __construct(S3Client $client, CloudFrontClient $cloudFront, $config, $bucket, $prefix = '', array $options = [])
    {
        $this->cloudFront = $cloudFront;
        $this->config = $config;

        parent::__construct($client, $bucket, $prefix, $options);
    }

    public function getUrl($path)
    {
        if (! is_null($url = $this->config('url'))) {
            return $this->concatPathToUrl($url, $this->getPathPrefix() . $path);
        }

        return $this->getClient()->getObjectUrl(
            $this->getBucket(), $this->getPathPrefix() . $path
        );
    }

    public function getSignedUrl($path, $expires, array $options = [])
    {
        if ($this->config('serve_via_cloudfront')) {
            $this->getCdnSignedUrl($path, $expires, $options);
        }

        return $this->getTemporaryUrl($path, $expires, $options);
    }

    public function getTemporaryUrl($path, $expires, $options)
    {
        $client = $this->getClient();

        $command = $client->getCommand('GetObject', array_merge([
            'Bucket' => $this->getBucket(),
            'Key' => $this->getPathPrefix() . $path,
        ], $options));

        return (string) $client->createPresignedRequest(
            $command,
            $expires
        )->getUri();
    }

    public function getCdnSignedUrl($path, $expires, $options)
    {
        $resourceUrl = $this->config['cloudfront.protocol'] . '://' . $this->config['cloudfront.domain'] . $path;

        // Create a signed URL for the resource using the canned policy
        $signedUrlCannedPolicy = $cloudFront->getSignedUrl(array_merge([
            'url' => $resourceUrl,
            'expires' => $expires,
            'private_key' => base_path($this->config['cloudfront.private_key_path']),
            'key_pair_id' => $this->config['cloudfront.key_pair_id'],
        ], $options));

        return $signedUrlCannedPolicy;
    }
}
