# UCNS - University of Calgary  Name Service

A decentralized, agent-based blockchain domain registration system built on Polygon, enabling seamless Web3 identity management through intelligent autonomous agents and smart contract orchestration.

## Team

- **Ali Mohammadi Ruzbahani** [30261140] - [@ruzbahani](https://github.com/ruzbahani)
- **Shuvam Agarwala** [30290444] - [@ShuvamAgarwala](https://github.com/Shuvam-Ruet)

**Course:** SENG 696 - Agent-Based Software Engineering  
**Instructor:** Professor Behrouz Far  
**University of Calgary** | Fall 2025


## Live Demo

🌐 **Web Interface:** https://ruzbahani.com/myprojects/ucns/

## Overview

UCNS represents a next-generation blockchain naming service that leverages agent-based architecture to provide autonomous, intelligent domain management on the Polygon network. The system employs three specialized smart contract agents that communicate and coordinate to deliver a comprehensive naming service comparable to traditional DNS but fully decentralized and blockchain-native.

### Core Agents

| Agent | Responsibility |
|-------|---------------|
| **Registry Agent** | Authoritative ownership and lifecycle management |
| **Resolver Agent** | Domain-to-address translation and record management |
| **Pricing Agent** | Dynamic cost calculation and market adaptation |

### Key Features

- 🔒 Decentralized ownership with zero counterparty risk
- ⚡ Fast on-chain resolution (~2s avg)
- 🎯 Multi-record support (address, text, content hash)
- 🤖 Autonomous agent coordination via smart contracts
- 💰 Dynamic pricing based on domain length and duration
- 🔄 Seamless renewal and transfer mechanisms

## Agent-Based Architecture

The UCNS multi-agent architecture follows GAIA-style analysis and design, where each smart contract is modeled as an autonomous agent with explicit roles, responsibilities, and interaction protocols. The UCNS ecosystem consists of three primary intelligent agents, each with distinct responsibilities and autonomous decision-making capabilities:

### Registry Agent (UCNSRegistry.sol)

The Registry Agent serves as the authoritative source for domain ownership and lifecycle management. This agent autonomously handles ownership transfers, expiration tracking, and permission management. It maintains a distributed ledger of all registered domains and their associated metadata, making independent decisions about ownership validity and transfer permissions based on predefined rules and temporal constraints.

The Registry Agent communicates with external actors through well-defined interfaces, accepting registration requests, processing ownership changes, and responding to ownership queries. Its autonomous nature ensures that domain ownership remains tamper-proof and verifiable without centralized intervention.

### Resolver Agent (UCNSResolver.sol)

The Resolver Agent functions as the translation layer between human-readable domain names and blockchain addresses. This intelligent agent stores and retrieves multiple types of records including Ethereum addresses, text records, and content hashes. It operates independently to resolve queries, update records, and maintain data consistency.

The Resolver Agent exhibits agent-like behavior by understanding context-specific queries and returning appropriate results based on the type of resolution requested. It coordinates with the Registry Agent to verify authorization before accepting record updates, demonstrating inter-agent communication and validation.

### Pricing Agent (PricingAgent.sol)

The Pricing Agent autonomously calculates registration costs based on domain characteristics and market conditions. This agent employs dynamic pricing algorithms that consider domain length and registration duration. It operates independently from the Registry and Resolver agents but provides critical pricing information that influences registration decisions.

The Pricing Agent demonstrates autonomous behavior through its ability to adjust pricing structures based on owner-defined parameters, responding to market conditions without requiring constant manual intervention. It communicates pricing decisions to the Registry Agent during the registration process, exemplifying agent coordination.

## System Architecture

```
     ┌─────────────────────────────────────────────────────────┐
     │                  User Interface (Web)                   │
     │            [MetaMask Integration • Ethers.js]           │
     └────────────────----────┬────────────────────────────────┘
                              │
                              │ Transaction Signing
                              │ State Queries
                              ▼
┌─────────────────────────────────────────────────---───────---------─┐
│                      Polygon Blockchain Network                     │
│                                                                     │
│  ┌──────────────┐      ┌──────────────┐      ┌───────────────┐      │
│  │   Registry   │◄────►│   Resolver   │      │    Pricing    │      │
│  │    Agent     │      │    Agent     │      │     Agent     │      │
│  └──────┬───────┘      └──────┬───────┘      └───────┬───────┘      │
│         │                     │                      │              │
│         │ Ownership           │ Resolution           │ Cost         │
│         │ Validation          │ Records              │ Calculation  │
│         │                     │                      │              │
│         └─────────────────────┼──────────────────────┘              │
│                               │                                     │
│                         Agent Communication                         │
│                         Protocol (Events)                           │
│                                                                     │
└──────────────────────────────────────────────────────------------───┘
```

**Communication Flow:**
1. User initiates domain registration via web interface
2. Registry Agent validates availability and queries Pricing Agent
3. Pricing Agent calculates cost based on domain parameters
4. Upon payment, Registry Agent records ownership atomically
5. Resolver Agent coordinates with Registry for authorized updates

## Agent Communication Protocol

The three agents communicate through a structured protocol built on Ethereum's smart contract messaging system. When a user initiates a domain registration, the following agent interactions occur:

The user request first reaches the Registry Agent, which queries the Pricing Agent to determine the required payment. The Pricing Agent autonomously calculates the cost and returns this information. Upon receiving valid payment, the Registry Agent records the ownership and can optionally coordinate with the Resolver Agent to establish initial resolution records. This multi-agent collaboration happens atomically on-chain, ensuring consistency and reliability.

Agent authorization follows a hierarchical model where the Registry Agent maintains ultimate authority over domain ownership, while delegating resolution responsibilities to the Resolver Agent for approved domains. This separation of concerns allows each agent to specialize and operate efficiently within its domain of expertise.

## Deployed Contracts

The UCNS system is live on Polygon Mainnet with the following verified smart contracts:

| Contract | Address | Explorer |
|----------|---------|----------|
| **Registry Agent** | `0xc9eD4B38E29C64d37cb83819D5eEcFD34EFdce0C` | [View on PolygonScan](https://polygonscan.com/address/0xc9eD4B38E29C64d37cb83819D5eEcFD34EFdce0C) |
| **Resolver Agent** | `0x2De897131ee8AC0538585887989E2314034F0b71` | [View on PolygonScan](https://polygonscan.com/address/0x2De897131ee8AC0538585887989E2314034F0b71) |
| **Pricing Agent** | `0x50F50124Ee00002379142cff115b0550240898B3` | [View on PolygonScan](https://polygonscan.com/address/0x50F50124Ee00002379142cff115b0550240898B3) |

**Network:** Polygon PoS Mainnet (Chain ID: 137)  
**Compiler:** Solidity 0.8.20+  
**Verification Status:** ✅ All contracts verified on PolygonScan

## Documentation

- **[Report 1A: System Specification](docs/reports/Report-1A-System-Specification.pdf)** ✅
- **[Report 1B: GAIA Design](docs/reports/Report-1B-GAIA-Design.pdf)** ✅
- **[Report 2: Detailed Development Document](docs/reports/Report-2.pdf)** ✅
- **[Project code: Smart Contracts](contracts/) and [Project code: Web Interface](webinterface/)** ✅
- 
## Technical Implementation

### Smart Contract Architecture

The system deploys three interconnected smart contracts on the Polygon network, each representing an autonomous agent:

**UCNSRegistry Contract**: Implements ownership management, expiration tracking, and operator permissions. The contract maintains mappings of domain hashes to owner addresses and expiration timestamps. It emits events for all state changes, enabling external systems to monitor agent activities.

**UCNSResolver Contract**: Manages the resolution of domains to various record types. The contract stores mappings from domain hashes to addresses, text records, and content hashes. It verifies authorization through the Registry Agent before accepting updates.

**PricingAgent Contract**: Calculates costs dynamically based on configurable price tiers. The contract implements a tiered pricing structure where domain length determines the base price category, and registration duration affects the total cost.

### Frontend Integration

The web interface (index.php) provides a user-friendly gateway to interact with the autonomous agents. Built with modern web technologies including HTML5, Bootstrap 5, and Ethers.js, the interface translates user intentions into agent-compatible transactions. The frontend handles MetaMask wallet integration, transaction signing, and real-time status updates from the blockchain.

Key features include domain search and registration, ownership verification, record management, and WHOIS-style lookups. The interface communicates with all three agents through Web3 provider connections, submitting transactions and reading state from the Polygon network.

## Core Functionality

### Domain Registration Process

Registration begins when a user submits a domain request through the frontend. The system first verifies domain availability by querying the Registry Agent. If available, it requests pricing information from the Pricing Agent based on the desired registration period. Upon user confirmation and payment, the Registry Agent atomically records the ownership with an expiration timestamp, establishing the domain on the blockchain.

### Domain Resolution

Resolution requests query the Resolver Agent with a domain name. The agent returns the associated Ethereum address or other record types based on what the domain owner has configured. This process happens entirely on-chain, ensuring censorship resistance and reliability. The Resolver Agent maintains multiple record types per domain, supporting various use cases from simple address resolution to complex metadata storage.

### Domain Renewal

Domain owners can extend their registration before expiration by interacting with the Registry Agent and providing additional payment calculated by the Pricing Agent. The renewal process atomically updates the expiration timestamp, ensuring continuous ownership. The agent-based architecture prevents domain hijacking by validating ownership before processing renewals.

### Access Control and Delegation

The Registry Agent implements sophisticated permission management allowing domain owners to designate operators who can manage domains on their behalf. This delegation model enables complex ownership structures while maintaining security. The Resolver Agent respects these permissions when processing record updates, demonstrating coordinated agent behavior.

## Deployment Configuration

The system is currently deployed and operational on Polygon Mainnet. The three smart contracts are verified and publicly accessible via PolygonScan. Frontend configuration points to the deployed contract addresses listed above.

For local development or testnet deployment, follow this sequence: first deploy the PricingAgent to establish pricing logic, then deploy the UCNSRegistry with the PricingAgent address, and finally deploy the UCNSResolver with the Registry address.

### Network Parameters

**Polygon Mainnet Configuration:**
- Network ID: 137
- RPC URL: https://polygon-rpc.com
- Block Explorer: https://polygonscan.com

**Polygon Mumbai Testnet Configuration:**
- Network ID: 80001
- RPC URL: https://rpc-mumbai.maticvigil.com
- Block Explorer: https://mumbai.polygonscan.com
- Faucet: https://faucet.polygon.technology

## Security Considerations

The agent-based architecture incorporates multiple security layers. Each agent validates inputs and enforces access controls before processing requests. The Registry Agent prevents unauthorized ownership changes through cryptographic verification. The Resolver Agent ensures only authorized parties can update records by checking permissions with the Registry Agent. The Pricing Agent operates with owner-only modification rights, preventing unauthorized price manipulation.

Reentrancy protection safeguards all payment-handling functions. Domain ownership transfers follow secure patterns with explicit approval requirements. The system emits comprehensive events for all state changes, enabling external monitoring and audit trails.

## Gas Optimization

Agent operations are optimized for minimal gas consumption on Polygon. The contracts use efficient storage patterns, packing related data into single storage slots where possible. View functions allow free queries without transaction costs. Batch operations could be implemented for users managing multiple domains, further reducing per-domain costs.

## Future Enhancements

The agent-based architecture supports extensibility through additional specialized agents. Potential enhancements include:

A Marketplace Agent could facilitate domain trading with escrow capabilities and automated price discovery. This agent would coordinate with the Registry Agent for ownership transfers and implement trustless exchange mechanisms.

A Reputation Agent could track domain usage patterns and assign trust scores. This agent would analyze transaction history and resolution frequency to provide reputation metrics useful for security applications.

A Governance Agent could enable decentralized decision-making about system parameters. Token holders could vote on pricing structures, registration rules, and system upgrades through this autonomous governance layer.

Machine learning integration could enhance the Pricing Agent with predictive analytics, adjusting prices based on demand patterns and market conditions. This would create a truly adaptive pricing system responsive to ecosystem dynamics.

## Installation

```bash
# Clone repository
git clone https://github.com/ruzbahani/UCNS-University-of-Calgary-Name-Service.git
cd ucns

# Configure Web3 provider
# Update contract addresses in frontend/config.js with deployed addresses

# Setup web server (Apache/Nginx with PHP 8.0+)
# Copy files to web root
cp -r frontend/* /var/www/html/ucns/

# For local development
cd frontend
php -S localhost:8080
```

## Usage

```bash
# Access web interface
# Open browser: https://ruzbahani.com/myprojects/ucns/
# Or locally: http://localhost:8080

# Connect MetaMask wallet to Polygon Mainnet
# Ensure you have MATIC tokens for gas fees

# Register a domain
# 1. Search for available domain
# 2. Select registration period (1-10 years)
# 3. Confirm transaction in MetaMask
# 4. Wait for blockchain confirmation

# Manage your domains
# - View registered domains in "My Domains" section
# - Update resolver records (address, text, content hash)
# - Renew domains before expiration
# - Transfer ownership to another address
```

**Web Interface Features:**
- Domain search and availability checking
- Real-time pricing calculation
- Domain registration with MetaMask
- WHOIS-style domain lookup
- Resolver record management
- Domain renewal and transfer

## Performance Metrics

| Metric | Target | Status |
|--------|--------|--------|
| Domain registration | ≤ 3s | ✅ Achieved |
| Domain resolution | ≤ 2s | ✅ Achieved |
| Record update | ≤ 2s | ✅ Achieved |
| Transaction gas cost | ~0.01 MATIC | ✅ Optimized |
| Contract verification | 100% | ✅ Complete |


## Development Setup

Clone the repository and install dependencies for local development:

```bash
git clone https://github.com/ruzbahani/UCNS-University-of-Calgary-Name-Service.git
cd ucns

# Install Hardhat for smart contract development
npm install --save-dev hardhat @nomiclabs/hardhat-ethers ethers

# Compile contracts
npx hardhat compile

# Run tests
npx hardhat test

# Deploy to local network
npx hardhat node
npx hardhat run scripts/deploy.js --network localhost
```

Configure your Web3 provider and deploy contracts using Hardhat. Update the contract addresses in the frontend configuration file (frontend/config.js) to point to your deployed instances.

For frontend development, ensure PHP 8.0+ is installed along with a web server supporting PHP. The interface requires MetaMask extension for blockchain interactions.

## Testing

Comprehensive testing ensures agent reliability and security. Test scripts should verify individual agent behavior, inter-agent communication protocols, and edge cases like expired domains and unauthorized access attempts. Use the Polygon Mumbai testnet for integration testing before mainnet deployment.

Test scenarios should include domain registration with various lengths and durations, ownership transfers between addresses, resolver record updates, and pricing calculations under different configurations. Simulate attack vectors including reentrancy attempts and authorization bypasses.

## Project Roadmap

- [x] Phase 1: Smart Contract Architecture Design
- [x] Phase 2: Agent Implementation (Registry, Resolver, Pricing)
- [x] Phase 3: Polygon Mainnet Deployment
- [x] Phase 4: Contract Verification on PolygonScan
- [x] Phase 5: Web Interface Development (PHP + Ethers.js)
- [x] Phase 6: MetaMask Integration & Testing
- [x] Phase 7: Production Launch

## Contributing

Contributions to the UCNS project are welcome. When proposing changes to agent behavior or adding new agents, ensure designs maintain the autonomous, decentralized nature of the system. Follow Solidity best practices for smart contract development and provide comprehensive tests for new functionality.

## License

MIT License - See [LICENSE](LICENSE)
**Academic Project:** SENG 696 @ University of Calgary (Fall 2025)
This project is developed for academic purposes at the University of Calgary. Please contact the project maintainers for licensing information regarding commercial use or derivative works.

## Technical Stack

**Blockchain Layer:** Polygon PoS Chain, Solidity 0.8.x, OpenZeppelin Contracts  
**Frontend Layer:** HTML5/PHP, Bootstrap 5, Ethers.js, MetaMask Integration  
**Development Tools:** Hardhat, Ethers.js, Polygon RPC Providers

## Contact

- **Ali Mohammadi Ruzbahani:** ali.mohammadiruzbaha@ucalgary.ca
- **GitHub:** [@ruzbahani](https://github.com/ruzbahani)
- **Project Repository:** https://github.com/ruzbahani/UCNS-University-of-Calgary-Name-Service
- **Live Demo:** https://ruzbahani.com/myprojects/ucns/UCNS-University-of-Calgary-Name-Service

## Acknowledgments

This project represents research into agent-based blockchain systems conducted at the University of Calgary. The autonomous agent architecture demonstrates how decentralized systems can achieve complex coordination through specialized, independent smart contract agents operating on trustless blockchain infrastructure.

---

**Project Status:** ✅ Production - Live on Polygon Mainnet  
**Current Version:** 1.0.0  
**Last Updated:** November 2025  
**Blockchain Network:** Polygon PoS (Chain ID: 137)  
**Smart Contract Language:** Solidity 0.8.20+  
**Academic Institution:** University of Calgary
