// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

contract LandTitleRegistry {
    struct LandTitle {
        string titleNumber;
        string hash;
        uint256 timestamp;
    }

    mapping(string => LandTitle) private landTitles;

    event LandTitleRegistered(string titleNumber, string hash, uint256 timestamp);

    function registerLandTitle(string memory _titleNumber, string memory _hash) public {
        require(bytes(_titleNumber).length > 0, "Title number required");
        require(bytes(_hash).length > 0, "Hash required");
        
        landTitles[_titleNumber] = LandTitle(_titleNumber, _hash, block.timestamp);
        
        emit LandTitleRegistered(_titleNumber, _hash, block.timestamp);
    }

    function getLandTitle(string memory _titleNumber) public view returns (string memory, string memory, uint256) {
        LandTitle memory landTitle = landTitles[_titleNumber];
        return (landTitle.titleNumber, landTitle.hash, landTitle.timestamp);
    }
}