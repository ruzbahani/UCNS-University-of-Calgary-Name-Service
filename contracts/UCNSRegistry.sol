// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

/**
 * ------------------------------------------------------------------------
 * Project: UCNS Name Service (UCNS) – .ucns on Polygon
 * File: UCNSRegistry.sol
 *
 * Agent Type: Registry Agent (Coordinator / Mediator)
 *
 * Agent Responsibilities:
 *  - Acts as the central autonomous agent for .ucns domain lifecycle
 *  - Coordinates with on-chain PricingAgent and ResolverAgent
 *  - Manages registration, renewal, transfer, and release of domains
 *  - Enforces naming policies, expiration, grace periods, and reserved names
 *  - Maintains global view over domain ownership and registry statistics
 *
 * Architectural Role:
 *  - Coordination layer in an agent-based architecture
 *  - Uses message-style interactions (function calls/events) to:
 *      * Query PricingAgent for dynamic MATIC-based pricing
 *      * Inform ResolverAgent about ownership changes
 *  - Serves as the primary point of interaction for off-chain agents
 *    (e.g., RegistrationAgent, UI clients, or integration services)
 *
 * Course: SENG 696 L01 (Fall 2025) – Agent-Based Software Engineering
 * Supervisor: Professor Behrouz Far
 *
 * Authors:
 *  - Ali Mohammadi Ruzbahani
 *      > Smart contract architecture, on-chain agents, Polygon integration
 *  - Shuvam Agarwala
 *      > Documentation, agent interaction modelling, design rationale
 *
 * Domain Space:
 *  - Top-Level Domain (TLD): .ucns
 *  - Target Network: Polygon
 * Version: 1.0.0
 * ------------------------------------------------------------------------
 */

interface IPricingAgent {
    function calculatePrice(uint256 domainLength) external view returns (uint256);
    function calculatePriceWithDuration(uint256 domainLength, uint256 durationInYears) external view returns (uint256);
}

interface IResolver {
    function setDomainOwner(bytes32 node, address owner) external;
}

contract UCNSRegistry {
    address public admin;
    IPricingAgent public pricingAgent;
    IResolver public resolverAgent;
    bool public isActive;

    string public constant TLD = "ucns";
    uint256 public constant MIN_REGISTRATION_DURATION = 365 days;
    uint256 public constant MAX_REGISTRATION_DURATION = 3650 days;
    uint256 public constant GRACE_PERIOD = 90 days;

    struct DomainRecord {
        address owner;
        uint256 registrationDate;
        uint256 expirationDate;
        bool exists;
        address resolver;
    }

    mapping(bytes32 => DomainRecord) private domains;
    mapping(address => bytes32[]) private userDomains;
    mapping(string => bool) private reservedNames;

    uint256 public totalDomainsRegistered;
    uint256 public totalActivedomains;

    event DomainRegistered(
        string indexed domainName,
        bytes32 indexed node,
        address indexed owner,
        uint256 expirationDate,
        uint256 pricePaid
    );

    event DomainRenewed(
        bytes32 indexed node,
        uint256 newExpirationDate,
        uint256 pricePaid
    );

    event DomainTransferred(
        bytes32 indexed node,
        address indexed previousOwner,
        address indexed newOwner
    );

    event ResolverUpdated(
        bytes32 indexed node,
        address indexed newResolver
    );

    event DomainReleased(
        bytes32 indexed node,
        string domainName
    );

    event AgentUpdated(string agentType, address newAgent);
    event NameReserved(string name);
    event NameUnreserved(string name);
    event AgentActivated();
    event AgentDeactivated();

    modifier onlyAdmin() {
        require(msg.sender == admin, "Registry: Only admin can call");
        _;
    }

    modifier onlyDomainOwner(bytes32 node) {
        require(domains[node].owner == msg.sender, "Registry: Only domain owner can call");
        _;
    }

    modifier whenActive() {
        require(isActive, "Registry: Agent is not active");
        _;
    }

    modifier validDomainName(string memory name) {
        require(bytes(name).length > 0, "Registry: Domain name cannot be empty");
        require(bytes(name).length <= 64, "Registry: Domain name too long");
        require(_isValidDomainName(name), "Registry: Invalid domain name format");
        require(!reservedNames[_toLower(name)], "Registry: Domain name is reserved");
        _;
    }

    constructor(address _pricingAgent, address _resolverAgent) {
        admin = msg.sender;
        pricingAgent = IPricingAgent(_pricingAgent);
        resolverAgent = IResolver(_resolverAgent);
        isActive = true;

        reservedNames["admin"] = true;
        reservedNames["ucns"] = true;
        reservedNames["root"] = true;
        reservedNames["system"] = true;
    }

    function registerDomain(
        string calldata domainName,
        uint256 durationInYears
    ) external payable whenActive validDomainName(domainName) returns (bytes32 node) {
        require(durationInYears >= 1 && durationInYears <= 10, "Registry: Duration must be 1-10 years");

        node = _generateNode(domainName);

        require(!domains[node].exists || _isExpired(node), "Registry: Domain already registered");

        uint256 domainLength = bytes(domainName).length;
        uint256 requiredPrice = pricingAgent.calculatePriceWithDuration(domainLength, durationInYears);

        require(msg.value >= requiredPrice, "Registry: Insufficient payment");

        uint256 expirationDate = block.timestamp + (durationInYears * 365 days);

        domains[node] = DomainRecord({
            owner: msg.sender,
            registrationDate: block.timestamp,
            expirationDate: expirationDate,
            exists: true,
            resolver: address(resolverAgent)
        });

        userDomains[msg.sender].push(node);

        totalDomainsRegistered++;
        totalActivedomains++;

        resolverAgent.setDomainOwner(node, msg.sender);

        emit DomainRegistered(domainName, node, msg.sender, expirationDate, msg.value);

        if (msg.value > requiredPrice) {
            payable(msg.sender).transfer(msg.value - requiredPrice);
        }

        return node;
    }

    function renewDomain(
        string calldata domainName,
        uint256 additionalYears
    ) external payable whenActive {
        require(additionalYears >= 1 && additionalYears <= 10, "Registry: Additional years must be 1-10");

        bytes32 node = _generateNode(domainName);

        require(domains[node].exists, "Registry: Domain not registered");
        require(
            !_isExpired(node) || block.timestamp <= domains[node].expirationDate + GRACE_PERIOD,
            "Registry: Domain expired beyond grace period"
        );

        uint256 domainLength = bytes(domainName).length;
        uint256 renewalPrice = pricingAgent.calculatePriceWithDuration(domainLength, additionalYears);

        require(msg.value >= renewalPrice, "Registry: Insufficient payment");

        if (_isExpired(node)) {
            domains[node].expirationDate = block.timestamp + (additionalYears * 365 days);
        } else {
            domains[node].expirationDate += (additionalYears * 365 days);
        }

        emit DomainRenewed(node, domains[node].expirationDate, msg.value);

        if (msg.value > renewalPrice) {
            payable(msg.sender).transfer(msg.value - renewalPrice);
        }
    }

    function transferDomain(
        string calldata domainName,
        address newOwner
    ) external whenActive {
        require(newOwner != address(0), "Registry: Invalid new owner");

        bytes32 node = _generateNode(domainName);

        require(domains[node].exists, "Registry: Domain not registered");
        require(!_isExpired(node), "Registry: Domain expired");
        require(domains[node].owner == msg.sender, "Registry: Only owner can transfer");

        address previousOwner = domains[node].owner;
        domains[node].owner = newOwner;

        userDomains[newOwner].push(node);

        resolverAgent.setDomainOwner(node, newOwner);

        emit DomainTransferred(node, previousOwner, newOwner);
    }

    function setResolver(
        string calldata domainName,
        address newResolver
    ) external whenActive {
        bytes32 node = _generateNode(domainName);

        require(domains[node].exists, "Registry: Domain not registered");
        require(!_isExpired(node), "Registry: Domain expired");
        require(domains[node].owner == msg.sender, "Registry: Only owner can set resolver");
        require(newResolver != address(0), "Registry: Invalid resolver");

        domains[node].resolver = newResolver;

        emit ResolverUpdated(node, newResolver);
    }

    function isDomainAvailable(string calldata domainName) external view returns (bool available) {
        if (reservedNames[_toLower(domainName)]) {
            return false;
        }

        bytes32 node = _generateNode(domainName);

        if (!domains[node].exists) {
            return true;
        }

        return _isExpired(node) && block.timestamp > domains[node].expirationDate + GRACE_PERIOD;
    }

    function getDomainInfo(string calldata domainName)
        external
        view
        returns (
            address owner,
            uint256 registrationDate,
            uint256 expirationDate,
            address resolver,
            bool isExpired
        )
    {
        bytes32 node = _generateNode(domainName);
        DomainRecord memory record = domains[node];

        require(record.exists, "Registry: Domain not registered");

        return (
            record.owner,
            record.registrationDate,
            record.expirationDate,
            record.resolver,
            _isExpired(node)
        );
    }

    function getDomainsByOwner(address owner) external view returns (bytes32[] memory) {
        return userDomains[owner];
    }

    function getRegistrationPrice(
        string calldata domainName,
        uint256 durationInYears
    ) external view returns (uint256 price) {
        uint256 domainLength = bytes(domainName).length;
        return pricingAgent.calculatePriceWithDuration(domainLength, durationInYears);
    }

    function releaseDomain(string calldata domainName) external onlyAdmin {
        bytes32 node = _generateNode(domainName);

        require(domains[node].exists, "Registry: Domain not registered");
        require(_isExpired(node), "Registry: Domain not expired");
        require(
            block.timestamp > domains[node].expirationDate + GRACE_PERIOD,
            "Registry: Grace period not over"
        );

        totalActivedomains--;
        delete domains[node];

        emit DomainReleased(node, domainName);
    }

    function reserveName(string calldata name) external onlyAdmin {
        reservedNames[_toLower(name)] = true;
        emit NameReserved(name);
    }

    function unreserveName(string calldata name) external onlyAdmin {
        reservedNames[_toLower(name)] = false;
        emit NameUnreserved(name);
    }

    function updatePricingAgent(address newPricingAgent) external onlyAdmin {
        require(newPricingAgent != address(0), "Registry: Invalid address");
        pricingAgent = IPricingAgent(newPricingAgent);
        emit AgentUpdated("pricing", newPricingAgent);
    }

    function updateResolverAgent(address newResolverAgent) external onlyAdmin {
        require(newResolverAgent != address(0), "Registry: Invalid address");
        resolverAgent = IResolver(newResolverAgent);
        emit AgentUpdated("resolver", newResolverAgent);
    }

    function withdrawFees(address payable recipient) external onlyAdmin {
        require(recipient != address(0), "Registry: Invalid recipient");
        uint256 balance = address(this).balance;
        require(balance > 0, "Registry: No balance");

        recipient.transfer(balance);
    }

    function activate() external onlyAdmin {
        isActive = true;
        emit AgentActivated();
    }

    function deactivate() external onlyAdmin {
        isActive = false;
        emit AgentDeactivated();
    }

    function transferAdmin(address newAdmin) external onlyAdmin {
        require(newAdmin != address(0), "Registry: Invalid address");
        admin = newAdmin;
    }

    function _generateNode(string memory domainName) internal pure returns (bytes32) {
        return keccak256(abi.encodePacked(_toLower(domainName)));
    }

    function _isExpired(bytes32 node) internal view returns (bool) {
        return block.timestamp > domains[node].expirationDate;
    }

    function _isValidDomainName(string memory name) internal pure returns (bool) {
        bytes memory b = bytes(name);

        for (uint256 i = 0; i < b.length; i++) {
            bytes1 char = b[i];

            if (
                !(char >= 0x30 && char <= 0x39) &&
                !(char >= 0x61 && char <= 0x7A) &&
                !(char >= 0x41 && char <= 0x5A) &&
                !(char == 0x2D)
            ) {
                return false;
            }

            if (i == 0 && char == 0x2D) return false;
            if (i == b.length - 1 && char == 0x2D) return false;
        }

        return true;
    }

    function _toLower(string memory str) internal pure returns (string memory) {
        bytes memory bStr = bytes(str);
        bytes memory bLower = new bytes(bStr.length);

        for (uint256 i = 0; i < bStr.length; i++) {
            if ((uint8(bStr[i]) >= 65) && (uint8(bStr[i]) <= 90)) {
                bLower[i] = bytes1(uint8(bStr[i]) + 32);
            } else {
                bLower[i] = bStr[i];
            }
        }

        return string(bLower);
    }

    function version() external pure returns (string memory) {
        return "1.0.0";
    }
}