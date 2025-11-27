// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

/**
 * ------------------------------------------------------------------------
 * Project: UCNS Name Service (UCNS) – .ucns on Polygon
 * File: UCNSResolver.sol
 *
 * Agent Type: Resolver Agent (Knowledge / Metadata Manager)
 *
 * Agent Responsibilities:
 *  - Maintains rich metadata records for .ucns domains
 *  - Binds abstract domain nodes (hashes) to:
 *      * Wallet addresses
 *      * Email, avatar, description, website URL
 *      * Social links (Twitter, GitHub, etc.)
 *      * Content hashes (e.g., IPFS CIDs)
 *      * Arbitrary custom key–value records
 *  - Exposes a stable read/write interface for off-chain agents and UIs
 *  - Synchronizes ownership view based on notifications from RegistryAgent
 *
 * Architectural Role:
 *  - Autonomous, specialized agent focused on the “what” (metadata),
 *    while the RegistryAgent focuses on the “who/when” (ownership & time)
 *  - Encapsulates all metadata-related logic and lifecycle behind
 *    an agent-centric interface (activate/deactivate, registry rebinding)
 *  - Supports extensibility via custom records without changing core schema
 *
 * Course: SENG 696 L01 (Fall 2025) – Agent-Based Software Engineering
 * Supervisor: Professor Behrouz Far
 *
 * Authors:
 *  - Ali Mohammadi Ruzbahani
 *      > Metadata schema design, resolver logic, agent lifecycle control
 *  - Shuvam Agarwala
 *      > Documentation, agent responsibilities & interaction diagrams
 *
 * Domain Space:
 *  - Top-Level Domain (TLD): .ucns
 *  - Target Network: Polygon
 *
 * Version: 1.0.0
 * ------------------------------------------------------------------------
 */

contract ucnsResolver {
    // Agent configuration
    address public registryAgent;
    bool public isActive;

    struct DomainMetadata {
        address addr;
        string email;
        string avatar;
        string description;
        string url;
        string twitter;
        string github;
        bytes contentHash;
        mapping(string => string) customRecords;
    }

    mapping(bytes32 => DomainMetadata) private domainRecords;
    mapping(bytes32 => address) public domainOwners;
    mapping(bytes32 => string[]) private customRecordKeys;

    event AddressChanged(bytes32 indexed node, address newAddress);
    event TextChanged(bytes32 indexed node, string key, string value);
    event ContentHashChanged(bytes32 indexed node, bytes hash);
    event MetadataUpdated(bytes32 indexed node, string metadataType);
    event AgentActivated();
    event AgentDeactivated();

    modifier onlyRegistry() {
        require(msg.sender == registryAgent, "Resolver: Only registry agent can call");
        _;
    }

    modifier onlyDomainOwner(bytes32 node) {
        require(domainOwners[node] == msg.sender, "Resolver: Only domain owner can update");
        _;
    }

    modifier whenActive() {
        require(isActive, "Resolver: Agent is not active");
        _;
    }

    constructor(address _registryAgent) {
        registryAgent = _registryAgent;
        isActive = true;
    }

    function setDomainOwner(bytes32 node, address owner) external onlyRegistry {
        domainOwners[node] = owner;
    }

    function setAddr(bytes32 node, address _addr) external whenActive onlyDomainOwner(node) {
        domainRecords[node].addr = _addr;
        emit AddressChanged(node, _addr);
        emit MetadataUpdated(node, "address");
    }

    function addr(bytes32 node) external view returns (address) {
        return domainRecords[node].addr;
    }


    function setEmail(bytes32 node, string calldata _email) external whenActive onlyDomainOwner(node) {
        domainRecords[node].email = _email;
        emit TextChanged(node, "email", _email);
        emit MetadataUpdated(node, "email");
    }

  
    function getEmail(bytes32 node) external view returns (string memory) {
        return domainRecords[node].email;
    }

    
    function setAvatar(bytes32 node, string calldata _avatar) external whenActive onlyDomainOwner(node) {
        domainRecords[node].avatar = _avatar;
        emit TextChanged(node, "avatar", _avatar);
        emit MetadataUpdated(node, "avatar");
    }

 
    function getAvatar(bytes32 node) external view returns (string memory) {
        return domainRecords[node].avatar;
    }
    function setDescription(bytes32 node, string calldata _description) external whenActive onlyDomainOwner(node) {
        domainRecords[node].description = _description;
        emit TextChanged(node, "description", _description);
        emit MetadataUpdated(node, "description");
    }
    function getDescription(bytes32 node) external view returns (string memory) {
        return domainRecords[node].description;
    }

   
    function setURL(bytes32 node, string calldata _url) external whenActive onlyDomainOwner(node) {
        domainRecords[node].url = _url;
        emit TextChanged(node, "url", _url);
        emit MetadataUpdated(node, "url");
    }
    function getURL(bytes32 node) external view returns (string memory) {
        return domainRecords[node].url;
    }
    function setTwitter(bytes32 node, string calldata _twitter) external whenActive onlyDomainOwner(node) {
        domainRecords[node].twitter = _twitter;
        emit TextChanged(node, "twitter", _twitter);
        emit MetadataUpdated(node, "twitter");
    }

    function getTwitter(bytes32 node) external view returns (string memory) {
        return domainRecords[node].twitter;
    }

 
    function setGithub(bytes32 node, string calldata _github) external whenActive onlyDomainOwner(node) {
        domainRecords[node].github = _github;
        emit TextChanged(node, "github", _github);
        emit MetadataUpdated(node, "github");
    }

    function getGithub(bytes32 node) external view returns (string memory) {
        return domainRecords[node].github;
    }


    function setContentHash(bytes32 node, bytes calldata hash) external whenActive onlyDomainOwner(node) {
        domainRecords[node].contentHash = hash;
        emit ContentHashChanged(node, hash);
        emit MetadataUpdated(node, "contentHash");
    }

 
    function getContentHash(bytes32 node) external view returns (bytes memory) {
        return domainRecords[node].contentHash;
    }

    function setCustomRecord(bytes32 node, string calldata key, string calldata value)
        external
        whenActive
        onlyDomainOwner(node)
    {
        if (bytes(domainRecords[node].customRecords[key]).length == 0) {
            customRecordKeys[node].push(key);
        }

        domainRecords[node].customRecords[key] = value;
        emit TextChanged(node, key, value);
        emit MetadataUpdated(node, "customRecord");
    }


    function getCustomRecord(bytes32 node, string calldata key) external view returns (string memory) {
        return domainRecords[node].customRecords[key];
    }

    function getCustomRecordKeys(bytes32 node) external view returns (string[] memory) {
        return customRecordKeys[node];
    }


    function setBulkMetadata(
        bytes32 node,
        address _addr,
        string calldata _email,
        string calldata _avatar,
        string calldata _description
    ) external whenActive onlyDomainOwner(node) {
        if (_addr != address(0)) {
            domainRecords[node].addr = _addr;
            emit AddressChanged(node, _addr);
        }
        if (bytes(_email).length > 0) {
            domainRecords[node].email = _email;
            emit TextChanged(node, "email", _email);
        }
        if (bytes(_avatar).length > 0) {
            domainRecords[node].avatar = _avatar;
            emit TextChanged(node, "avatar", _avatar);
        }
        if (bytes(_description).length > 0) {
            domainRecords[node].description = _description;
            emit TextChanged(node, "description", _description);
        }

        emit MetadataUpdated(node, "bulk");
    }
    function getMetadata(bytes32 node)
        external
        view
        returns (
            address _addr,
            string memory _email,
            string memory _avatar,
            string memory _description,
            string memory _url,
            string memory _twitter,
            string memory _github,
            bytes memory _contentHash
        )
    {
        DomainMetadata storage metadata = domainRecords[node];
        return (
            metadata.addr,
            metadata.email,
            metadata.avatar,
            metadata.description,
            metadata.url,
            metadata.twitter,
            metadata.github,
            metadata.contentHash
        );
    }


    function updateRegistryAgent(address _registryAgent) external {
        require(msg.sender == registryAgent, "Resolver: Only current registry can update");
        require(_registryAgent != address(0), "Resolver: Invalid address");
        registryAgent = _registryAgent;
    }




    function activate() external {
        require(msg.sender == registryAgent, "Resolver: Only registry can activate");
        isActive = true;
        emit AgentActivated();
    }


    function deactivate() external {
        require(msg.sender == registryAgent, "Resolver: Only registry can deactivate");
        isActive = false;
        emit AgentDeactivated();
    }
}