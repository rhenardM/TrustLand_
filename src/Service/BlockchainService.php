<?php

namespace App\Service;

// use Web3\Web3;
// use Web3\Contract;
// use Web3\Providers\HttpProvider;
// use Web3\RequestManagers\HttpRequestManager;

// use Symfony\Component\DependencyInjection\Attribute\AsService;

//#[AsService]  // Indique à Symfony que c'est un service
// class BlockchainService
// {
//     private Web3 $web3;
//     private Contract $contract;
//     private string $contractAddress;
//     private string $account;

//     public function __construct(string $ethereumNode)
//     {
//         $this->web3 = new Web3(new HttpProvider(new HttpRequestManager($ethereumNode, 10)));

//         // Adresse du contrat déployé
//         $this->contractAddress = '0xdbD0183e6B85b7Ca184F83f2be714DB5b621f6ef';

//         // Adresse du propriétaire (l'adresse du compte de Ganache)
//         $this->account = '0x71977f3bbBEAcd844aF2b9B48aAD66AA5eD26d17'; 

//         // ABI du contrat
//         $abi = '[
//                     {
//                     "anonymous": false,
//                     "inputs": [
//                         {
//                         "indexed": false,
//                         "internalType": "string",
//                         "name": "titleNumber",
//                         "type": "string"
//                         },
//                         {
//                         "indexed": false,
//                         "internalType": "string",
//                         "name": "hash",
//                         "type": "string"
//                         },
//                         {
//                         "indexed": false,
//                         "internalType": "uint256",
//                         "name": "timestamp",
//                         "type": "uint256"
//                         }
//                     ],
//                     "name": "LandTitleRegistered",
//                     "type": "event"
//                     },
//                     {
//                     "inputs": [
//                         {
//                         "internalType": "string",
//                         "name": "_titleNumber",
//                         "type": "string"
//                         }
//                     ],
//                     "name": "getLandTitle",
//                     "outputs": [
//                         {
//                         "internalType": "string",
//                         "name": "",
//                         "type": "string"
//                         },
//                         {
//                         "internalType": "string",
//                         "name": "",
//                         "type": "string"
//                         },
//                         {
//                         "internalType": "uint256",
//                         "name": "",
//                         "type": "uint256"
//                         }
//                     ],
//                     "stateMutability": "view",
//                     "type": "function"
//                     },
//                     {
//                     "inputs": [
//                         {
//                         "internalType": "string",
//                         "name": "_titleNumber",
//                         "type": "string"
//                         },
//                         {
//                         "internalType": "string",
//                         "name": "_hash",
//                         "type": "string"
//                         }
//                     ],
//                     "name": "registerLandTitle",
//                     "outputs": [],
//                     "stateMutability": "nonpayable",
//                     "type": "function"
//                     }
//                 ]';

//         // Initialisation du contrat
//         $this->contract = new Contract($this->web3->provider, $abi);
//     }

//     public function storeLandTitle(string $titleNumber, string $hash)
//     {
//         $this->contract->at($this->contractAddress)->send('registerLandTitle', $titleNumber, $hash, [
//             'from' => $this->account
//         ], function ($err, $result) {
//             if ($err !== null) {
//                 throw new \Exception('Erreur : ' . $err->getMessage());
//             }
//             return $result;
//         });
//     }
// }

//

use Web3\Web3;
use Web3\Contract;
use Web3\Providers\HttpProvider;
use Psr\Log\LoggerInterface;
use React\Async;
use React\Promise\Deferred;



class BlockchainService
{
    private const CONTRACT_ADDRESS = '0xdbD0183e6B85b7Ca184F83f2be714DB5b621f6ef';
    private const ACCOUNT = '0x71977f3bbBEAcd844aF2b9B48aAD66AA5eD26d17';

    private Web3 $web3;
    private Contract $contract;
    private LoggerInterface $logger;

    public function __construct(BlockchainConfig $config, LoggerInterface $logger)
    {
        $this->web3 = new Web3(new HttpProvider($config->getEthereumNode(), 10));
        $this->contract = new Contract($this->web3->provider, $this->getAbi());
        $this->logger = $logger;
    }


    public function storeLandTitle(string $titleNumber, string $hash): ?string
    {
        if (empty($titleNumber) || empty($hash)) {
            throw new \InvalidArgumentException('Title number and hash must not be empty.');
        }

        $this->logger->info('Attempting to store land title in the blockchain.', [
            'titleNumber' => $titleNumber,
            'hash' => $hash,
        ]);

        $deferred = new Deferred();

        try {
            $this->contract->at(self::CONTRACT_ADDRESS)->send(
                'registerLandTitle',
                $titleNumber,
                $hash,
                ['from' => self::ACCOUNT],
                function ($err, $result) use ($deferred) {
                    if ($err !== null) {
                        $this->logger->error('Error while registering land title: ' . $err->getMessage());
                        $deferred->reject(new \RuntimeException('Blockchain error: ' . $err->getMessage()));
                    } else {
                        $this->logger->info('Transaction successful.', ['txHash' => $result]);
                        $deferred->resolve($result);
                    }
                }
            );

            // Attend la résolution de la promesse avec React\Async\await
            $txHash = Async\await($deferred->promise());
            return $txHash;
        } catch (\Throwable $e) {
            $this->logger->error('Exception in storeLandTitle: ' . $e->getMessage());
            return null;
        }
    }
    private function getAbi(): string
    {
        return json_encode([
            [
                "anonymous" => false,
                "inputs" => [
                    [
                        "indexed" => false,
                        "internalType" => "string",
                        "name" => "titleNumber",
                        "type" => "string"
                    ],
                    [
                        "indexed" => false,
                        "internalType" => "string",
                        "name" => "hash",
                        "type" => "string"
                    ],
                    [
                        "indexed" => false,
                        "internalType" => "uint256",
                        "name" => "timestamp",
                        "type" => "uint256"
                    ]
                ],
                "name" => "LandTitleRegistered",
                "type" => "event"
            ],
            [
                "inputs" => [
                    [
                        "internalType" => "string",
                        "name" => "_titleNumber",
                        "type" => "string"
                    ]
                ],
                "name" => "getLandTitle",
                "outputs" => [
                    [
                        "internalType" => "string",
                        "name" => "",
                        "type" => "string"
                    ],
                    [
                        "internalType" => "string",
                        "name" => "",
                        "type" => "string"
                    ],
                    [
                        "internalType" => "uint256",
                        "name" => "",
                        "type" => "uint256"
                    ]
                ],
                "stateMutability" => "view",
                "type" => "function"
            ],
            [
                "inputs" => [
                    [
                        "internalType" => "string",
                        "name" => "_titleNumber",
                        "type" => "string"
                    ],
                    [
                        "internalType" => "string",
                        "name" => "_hash",
                        "type" => "string"
                    ]
                ],
                "name" => "registerLandTitle",
                "outputs" => [],
                "stateMutability" => "nonpayable",
                "type" => "function"
            ]
        ]);
    }
}
