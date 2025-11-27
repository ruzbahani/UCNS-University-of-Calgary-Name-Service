// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

/**
 * ------------------------------------------------------------------------
 * Project: UCNS Name Service (UCNS) – .ucns on Polygon
 * File: PricingAgent.sol
 *
 * Agent Type: Pricing Agent (Economic / Resource-Allocation Agent)
 *
 * Agent Responsibilities:
 *  - Encapsulates all pricing logic for .ucns domain registrations
 *  - Computes base price in MATIC as a function of:
 *      * Domain label length (shorter names are more expensive)
 *      * Registration duration in years (multi-year pricing)
 *  - Exposes a clean on-chain interface to the RegistryAgent:
 *      * calculatePrice(domainLength)
 *      * calculatePriceWithDuration(domainLength, durationInYears)
 *  - Allows runtime reconfiguration of pricing policy by the owner agent
 *    without modifying the registry or resolver contracts
 *
 * Architectural Role:
 *  - Dedicated economic agent decoupled from ownership and metadata
 *  - Supports agent-based separation of concerns:
 *      * RegistryAgent → coordination and lifecycle
 *      * PricingAgent → economic strategy
 *      * ResolverAgent → knowledge/metadata management
 *  - Enables future replacement/upgrade of pricing strategies by
 *    redirecting the RegistryAgent to a new PricingAgent instance
 *
 * Course: SENG 696 L01 (Fall 2025) – Agent-Based Software Engineering
 * Supervisor: Professor Behrouz Far
 *
 * Authors:
 *  - Ali Mohammadi Ruzbahani
 *      > Dynamic pricing model, on-chain economic agent implementation
 *  - Shuvam Agarwala
 *      > Documentation, rationale for agent separation and policies
 *
 * Domain Space:
 *  - Top-Level Domain (TLD): .ucns
 *  - Target Network: Polygon
 *
 * Version: 1.0.0
 * ------------------------------------------------------------------------
 */

contract PricingAgent {
    address public owner;
    bool public isActive;

    mapping(uint256 => uint256) public lengthPrices;
    uint256 public basePrice;

    event PriceUpdated(uint256 length, uint256 newPrice);
    event BasePriceUpdated(uint256 newBasePrice);
    event AgentActivated();
    event AgentDeactivated();

    modifier onlyOwner() {
        require(msg.sender == owner, "PricingAgent: Only owner can call");
        _;
    }

    modifier whenActive() {
        require(isActive, "PricingAgent: Agent is not active");
        _;
    }

    constructor() {
        owner = msg.sender;
        isActive = true;

        lengthPrices[1] = 0.01 ether;     
        lengthPrices[2] = 0.005 ether;    
        lengthPrices[3] = 0.002 ether;   
        lengthPrices[4] = 0.001 ether;    

        basePrice = 0.0005 ether;         
    }

    function _calculatePriceInternal(uint256 domainLength) internal view returns (uint256) {
        if (domainLength <= 4) {
            return lengthPrices[domainLength];
        }
        return basePrice;
    }

    function calculatePrice(uint256 domainLength) external view whenActive returns (uint256 price) {
        require(domainLength > 0, "PricingAgent: Invalid domain length");
        return _calculatePriceInternal(domainLength);
    }

    function calculatePriceWithDuration(
        uint256 domainLength,
        uint256 durationInYears
    ) external view whenActive returns (uint256 totalPrice) {
        require(domainLength > 0, "PricingAgent: Invalid domain length");
        require(durationInYears > 0 && durationInYears <= 10, "PricingAgent: Duration must be 1-10 years");

        uint256 unitPrice = _calculatePriceInternal(domainLength);
        totalPrice = unitPrice * durationInYears;

        return totalPrice;
    }

    function updateLengthPrice(uint256 length, uint256 newPrice) external onlyOwner {
        require(length > 0 && length <= 4, "PricingAgent: Length must be 1-4");
        require(newPrice > 0, "PricingAgent: Price must be positive");

        lengthPrices[length] = newPrice;
        emit PriceUpdated(length, newPrice);
    }

    function updateBasePrice(uint256 newBasePrice) external onlyOwner {
        require(newBasePrice > 0, "PricingAgent: Price must be positive");

        basePrice = newBasePrice;
        emit BasePriceUpdated(newBasePrice);
    }

    function getPrice(uint256 length) external view returns (uint256) {
        if (length <= 4) {
            return lengthPrices[length];
        }
        return basePrice;
    }

    function activate() external onlyOwner {
        isActive = true;
        emit AgentActivated();
    }

    function deactivate() external onlyOwner {
        isActive = false;
        emit AgentDeactivated();
    }

    function transferOwnership(address newOwner) external onlyOwner {
        require(newOwner != address(0), "PricingAgent: Invalid address");
        owner = newOwner;
    }
}