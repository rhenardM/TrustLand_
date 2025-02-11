<?php 

namespace App\Service;

class BlockchainConfig
{
    private string $ethereumNode;

    public function __construct(string $ethereumNode)
    {
        $this->ethereumNode = $ethereumNode;
    }

    public function getEthereumNode(): string
    {
        return $this->ethereumNode;
    }
}