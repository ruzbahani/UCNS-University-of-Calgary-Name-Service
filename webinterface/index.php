<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCNS - University of Calgary Name Service</title>
    <script src="https://cdn.jsdelivr.net/npm/ethers@6.13.2/dist/ethers.umd.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        .wallet-section {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 2px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .wallet-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #dc3545;
            animation: pulse 2s infinite;
        }
        .status-indicator.connected {
            background: #28a745;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-success:hover {
            background: #218838;
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }
        .tabs {
            display: flex;
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            overflow-x: auto;
        }
        .tab {
            padding: 20px 30px;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 1em;
            font-weight: 600;
            color: #6c757d;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        .tab:hover {
            background: #e9ecef;
            color: #495057;
        }
        .tab.active {
            color: #667eea;
            border-bottom: 3px solid #667eea;
            background: white;
        }
        .tab-content {
            padding: 40px;
        }
        .tab-pane {
            display: none;
        }
        .tab-pane.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
        }
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .search-box {
            position: relative;
        }
        .search-box input {
            padding-right: 100px;
        }
        .tld-suffix {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-weight: 600;
            pointer-events: none;
        }
        .result-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin-top: 20px;
            border-left: 4px solid #667eea;
        }
        .result-card h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        .info-value {
            color: #6c757d;
            word-break: break-all;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
        }
        .status-available {
            background: #d4edda;
            color: #155724;
        }
        .status-taken {
            background: #f8d7da;
            color: #721c24;
        }
        .status-expired {
            background: #fff3cd;
            color: #856404;
        }
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
        .alert.show {
            display: block;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateX(-20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
        .price-calculator {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        .price-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
        }
        .price-amount {
            font-size: 2.5em;
            font-weight: bold;
            margin: 15px 0;
        }
        .price-label {
            opacity: 0.9;
            font-size: 1.1em;
        }
        .metadata-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .footer {
            background: #2c3e50;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .footer h4 {
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        .footer p {
            margin: 8px 0;
            opacity: 0.9;
        }
        .footer .contract-addresses {
            margin-top: 20px;
            padding: 20px;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }
        .footer .contract-addresses div {
            margin: 5px 0;
        }
        .helper-text {
            font-size: 0.9em;
            color: #6c757d;
            margin-top: 5px;
        }
        .duration-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        .duration-btn {
            padding: 12px;
            border: 2px solid #e9ecef;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        .duration-btn:hover {
            border-color: #667eea;
            background: #f8f9fa;
        }
        .duration-btn.selected {
            border-color: #667eea;
            background: #667eea;
            color: white;
        }
        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.8em;
            }
            .tab-content {
                padding: 20px;
            }
            .price-calculator {
                grid-template-columns: 1fr;
            }
            .metadata-grid {
                grid-template-columns: 1fr;
            }
            .wallet-section {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 UCNS - University of Calgary Name Service</h1>
            <p>Decentralized Domain System on Polygon Mainnet</p>
        </div>

        <div class="wallet-section">
            <div class="wallet-info">
                <div class="status-indicator" id="statusIndicator"></div>
                <span id="walletStatus">Not Connected</span>
            </div>
            <button class="btn btn-primary" id="connectWalletBtn" onclick="connectWallet()">
                Connect Wallet
            </button>
        </div>

        <div class="tabs">
            <button class="tab active" data-tab="whois" onclick="switchTab('whois', this)">🔍 WHOIS Lookup</button>
            <button class="tab" data-tab="availability" onclick="switchTab('availability', this)">✓ Check Availability</button>
            <button class="tab" data-tab="register" onclick="switchTab('register', this)">📝 Register Domain</button>
            <button class="tab" data-tab="metadata" onclick="switchTab('metadata', this)">⚙️ Manage Metadata</button>
            <button class="tab" data-tab="pricing" onclick="switchTab('pricing', this)">💰 Pricing Info</button>
        </div>

        <div class="tab-content">
            <div id="whois" class="tab-pane active">
                <h2>🔍 WHOIS Lookup</h2>
                <p class="helper-text">Check detailed information about any registered domain (no wallet connection required)</p>
                <div class="alert alert-info show" style="margin-bottom: 20px;">
                    <strong>ℹ️ Domain Naming Rules:</strong><br>
                    • Only letters (a-z), numbers (0-9), and hyphens (-) allowed<br>
                    • Cannot start or end with hyphen<br>
                    • Maximum 64 characters<br>
                    • Reserved names: admin, ucns, root, system
                </div>
                <div class="form-group">
                    <label for="whoisDomain">Domain Name</label>
                    <div class="search-box">
                        <input type="text" id="whoisDomain" class="form-control" placeholder="Enter domain name">
                        <span class="tld-suffix">.ucns</span>
                    </div>
                </div>
                <button class="btn btn-primary" onclick="whoisLookup()">
                    Lookup Domain
                </button>
                <div id="whoisAlert" class="alert"></div>
                <div id="whoisResult"></div>
            </div>

            <div id="availability" class="tab-pane">
                <h2>✓ Check Domain Availability</h2>
                <p class="helper-text">See if a domain is available for registration (no wallet connection required)</p>
                <div class="alert alert-info show" style="margin-bottom: 20px;">
                    <strong>ℹ️ Domain Naming Rules:</strong><br>
                    • Only letters (a-z), numbers (0-9), and hyphens (-) allowed<br>
                    • Cannot start or end with hyphen<br>
                    • Maximum 64 characters<br>
                    • Reserved names: admin, ucns, root, system
                </div>
                <div class="form-group">
                    <label for="availabilityDomain">Domain Name</label>
                    <div class="search-box">
                        <input type="text" id="availabilityDomain" class="form-control" placeholder="Enter domain name">
                        <span class="tld-suffix">.ucns</span>
                    </div>
                </div>
                <button class="btn btn-primary" onclick="checkAvailability()">
                    Check Availability
                </button>
                <div id="availabilityAlert" class="alert"></div>
                <div id="availabilityResult"></div>
            </div>

            <div id="register" class="tab-pane">
                <h2>📝 Register New Domain</h2>
                <p class="helper-text">Register a new .ucns domain (requires wallet connection)</p>
                <div class="alert alert-info show" style="margin-bottom: 20px;">
                    <strong>💡 Registration Tips:</strong><br>
                    • Shorter domains are more expensive (1-4 chars)<br>
                    • Choose 1-10 years registration period<br>
                    • Make sure you have enough MATIC for gas fees<br>
                    • Domain names are case-insensitive
                </div>
                <div class="form-group">
                    <label for="registerDomain">Domain Name</label>
                    <div class="search-box">
                        <input type="text" id="registerDomain" class="form-control" placeholder="Enter domain name">
                        <span class="tld-suffix">.ucns</span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Registration Duration</label>
                    <div class="duration-options">
                        <div class="duration-btn selected" data-years="1" onclick="selectDuration(1, this)">1 Year</div>
                        <div class="duration-btn" data-years="2" onclick="selectDuration(2, this)">2 Years</div>
                        <div class="duration-btn" data-years="3" onclick="selectDuration(3, this)">3 Years</div>
                        <div class="duration-btn" data-years="5" onclick="selectDuration(5, this)">5 Years</div>
                        <div class="duration-btn" data-years="10" onclick="selectDuration(10, this)">10 Years</div>
                    </div>
                    <input type="hidden" id="registrationDuration" value="1">
                </div>
                <button class="btn btn-success" onclick="registerDomain()">
                    Register Domain
                </button>
                <div id="registerAlert" class="alert"></div>
                <div id="registerResult"></div>
            </div>

            <div id="metadata" class="tab-pane">
                <h2>⚙️ Manage Domain Metadata</h2>
                <p class="helper-text">Set metadata for your domains (requires wallet connection and domain ownership)</p>
                <div class="alert alert-info show" style="margin-bottom: 20px;">
                    <strong>⚙️ Metadata Management:</strong><br>
                    • Only domain owners can update metadata<br>
                    • Cannot update expired domains<br>
                    • Each field update requires a transaction<br>
                    • Leave fields empty to keep current values
                </div>
                <div class="form-group">
                    <label for="metadataDomain">Domain Name</label>
                    <div class="search-box">
                        <input type="text" id="metadataDomain" class="form-control" placeholder="Enter your domain name">
                        <span class="tld-suffix">.ucns</span>
                    </div>
                </div>
                <button class="btn btn-secondary" onclick="loadDomainMetadata()">
                    Load Current Metadata
                </button>
                <div id="metadataAlert" class="alert"></div>
                <div id="metadataForm" style="display: none; margin-top: 30px;">
                    <h3>Update Metadata</h3>
                    <div class="metadata-grid">
                        <div class="form-group">
                            <label for="metaAddress">Wallet Address</label>
                            <input type="text" id="metaAddress" class="form-control" placeholder="0x...">
                        </div>
                        <div class="form-group">
                            <label for="metaEmail">Email</label>
                            <input type="email" id="metaEmail" class="form-control" placeholder="your@email.com">
                        </div>
                        <div class="form-group">
                            <label for="metaAvatar">Avatar URL</label>
                            <input type="text" id="metaAvatar" class="form-control" placeholder="https://...">
                        </div>
                        <div class="form-group">
                            <label for="metaURL">Website URL</label>
                            <input type="text" id="metaURL" class="form-control" placeholder="https://...">
                        </div>
                        <div class="form-group">
                            <label for="metaTwitter">Twitter Handle</label>
                            <input type="text" id="metaTwitter" class="form-control" placeholder="@username">
                        </div>
                        <div class="form-group">
                            <label for="metaGithub">GitHub Username</label>
                            <input type="text" id="metaGithub" class="form-control" placeholder="username">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="metaDescription">Description</label>
                        <textarea id="metaDescription" class="form-control" rows="3" placeholder="Describe your domain..."></textarea>
                    </div>
                    <button class="btn btn-success" onclick="updateBulkMetadata()">
                        Update Metadata
                    </button>
                </div>
            </div>

            <div id="pricing" class="tab-pane">
                <h2>💰 Domain Pricing Information</h2>
                <p class="helper-text">Calculate registration costs based on domain length and duration</p>
                <div class="alert alert-info show" style="margin-bottom: 20px;">
                    <strong>💰 Pricing Structure (example):</strong><br>
                    • 1 character: 0.01 MATIC/year<br>
                    • 2 characters: 0.005 MATIC/year<br>
                    • 3 characters: 0.002 MATIC/year<br>
                    • 4 characters: 0.001 MATIC/year<br>
                    • 5+ characters: 0.0005 MATIC/year
                </div>
                <div class="form-group">
                    <label for="pricingDomain">Domain Name</label>
                    <div class="search-box">
                        <input type="text" id="pricingDomain" class="form-control" placeholder="Enter domain name">
                        <span class="tld-suffix">.ucns</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="pricingDuration">Duration (Years)</label>
                    <input type="number" id="pricingDuration" class="form-control" value="1" min="1" max="10">
                </div>
                <button class="btn btn-primary" onclick="calculatePrice()">
                    Calculate Price
                </button>
                <div id="pricingAlert" class="alert"></div>
                <div id="pricingResult"></div>
            </div>
        </div>

        <div class="footer">
            <h4>📚 Academic Project Information</h4>
            <p><strong>Course:</strong> SENG 696 L01 (Fall 2025) - Agent-Based Software Engineering</p>
            <p><strong>Supervisor:</strong> Professor Behrouz Far</p>
            <p><strong>Authors:</strong> Ali Mohammadi Ruzbahani & Shuvam Agarwala</p>
            <p><strong>Institution:</strong> University of Calgary</p>
            <p><strong>Network:</strong> Polygon Mainnet (Chain ID: 137)</p>
            <div class="contract-addresses">
                <div><strong>Registry:</strong> 0xc9eD4B38E29C64d37cb83819D5eEcFD34EFdce0C</div>
                <div><strong>Resolver:</strong> 0x2De897131ee8AC0538585887989E2314034F0b71</div>
                <div><strong>Pricing Agent:</strong> 0x50F50124Ee00002379142cff115b0550240898B3</div>
            </div>
            <p style="margin-top: 20px; font-size: 0.9em;">
                Autonomous multi-agent architecture: Registry, Resolver, and Pricing agents coordinating on-chain domain lifecycle.
            </p>
        </div>
    </div>

    <script>
        const REGISTRY_ADDRESS = '0xc9eD4B38E29C64d37cb83819D5eEcFD34EFdce0C';
        const RESOLVER_ADDRESS = '0x2De897131ee8AC0538585887989E2314034F0b71';
        const PRICING_ADDRESS = '0x50F50124Ee00002379142cff115b0550240898B3';
        const POLYGON_CHAIN_ID_HEX = '0x89';
        const REGISTRY_ABI = [
            {"inputs":[{"internalType":"string","name":"domainName","type":"string"}],"name":"isDomainAvailable","outputs":[{"internalType":"bool","name":"available","type":"bool"}],"stateMutability":"view","type":"function"},
            {"inputs":[{"internalType":"string","name":"domainName","type":"string"}],"name":"getDomainInfo","outputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"uint256","name":"registrationDate","type":"uint256"},{"internalType":"uint256","name":"expirationDate","type":"uint256"},{"internalType":"address","name":"resolver","type":"address"},{"internalType":"bool","name":"isExpired","type":"bool"}],"stateMutability":"view","type":"function"},
            {"inputs":[{"internalType":"string","name":"domainName","type":"string"},{"internalType":"uint256","name":"durationInYears","type":"uint256"}],"name":"getRegistrationPrice","outputs":[{"internalType":"uint256","name":"price","type":"uint256"}],"stateMutability":"view","type":"function"},
            {"inputs":[{"internalType":"string","name":"domainName","type":"string"},{"internalType":"uint256","name":"durationInYears","type":"uint256"}],"name":"registerDomain","outputs":[{"internalType":"bytes32","name":"node","type":"bytes32"}],"stateMutability":"payable","type":"function"}
        ];
        const RESOLVER_ABI = [
            {"inputs":[{"internalType":"bytes32","name":"node","type":"bytes32"}],"name":"getMetadata","outputs":[{"internalType":"address","name":"_addr","type":"address"},{"internalType":"string","name":"_email","type":"string"},{"internalType":"string","name":"_avatar","type":"string"},{"internalType":"string","name":"_description","type":"string"},{"internalType":"string","name":"_url","type":"string"},{"internalType":"string","name":"_twitter","type":"string"},{"internalType":"string","name":"_github","type":"string"},{"internalType":"bytes","name":"_contentHash","type":"bytes"}],"stateMutability":"view","type":"function"},
            {"inputs":[{"internalType":"bytes32","name":"node","type":"bytes32"},{"internalType":"address","name":"_addr","type":"address"},{"internalType":"string","name":"_email","type":"string"},{"internalType":"string","name":"_avatar","type":"string"},{"internalType":"string","name":"_description","type":"string"}],"name":"setBulkMetadata","outputs":[],"stateMutability":"nonpayable","type":"function"},
            {"inputs":[{"internalType":"bytes32","name":"node","type":"bytes32"},{"internalType":"string","name":"_url","type":"string"}],"name":"setURL","outputs":[],"stateMutability":"nonpayable","type":"function"},
            {"inputs":[{"internalType":"bytes32","name":"node","type":"bytes32"},{"internalType":"string","name":"_twitter","type":"string"}],"name":"setTwitter","outputs":[],"stateMutability":"nonpayable","type":"function"},
            {"inputs":[{"internalType":"bytes32","name":"node","type":"bytes32"},{"internalType":"string","name":"_github","type":"string"}],"name":"setGithub","outputs":[],"stateMutability":"nonpayable","type":"function"}
        ];
        const PRICING_ABI = [
            {"inputs":[{"internalType":"uint256","name":"domainLength","type":"uint256"}],"name":"calculatePrice","outputs":[{"internalType":"uint256","name":"price","type":"uint256"}],"stateMutability":"view","type":"function"},
            {"inputs":[{"internalType":"uint256","name":"domainLength","type":"uint256"},{"internalType":"uint256","name":"durationInYears","type":"uint256"}],"name":"calculatePriceWithDuration","outputs":[{"internalType":"uint256","name":"totalPrice","type":"uint256"}],"stateMutability":"view","type":"function"}
        ];
        let rpcProvider;
        let walletProvider;
        let signer;
        let registryRead;
        let resolverRead;
        let pricingRead;
        let registryWrite;
        let resolverWrite;
        let pricingWrite;
        let currentAccount = null;
        function initReadOnly() {
            rpcProvider = new ethers.JsonRpcProvider('https://polygon-rpc.com');
            registryRead = new ethers.Contract(REGISTRY_ADDRESS, REGISTRY_ABI, rpcProvider);
            resolverRead = new ethers.Contract(RESOLVER_ADDRESS, RESOLVER_ABI, rpcProvider);
            pricingRead = new ethers.Contract(PRICING_ADDRESS, PRICING_ABI, rpcProvider);
        }
        window.addEventListener('load', function() {
            initReadOnly();
            if (window.ethereum) {
                checkConnection();
            }
        });
        async function checkConnection() {
            try {
                const accounts = await window.ethereum.request({ method: 'eth_accounts' });
                if (accounts.length > 0) {
                    currentAccount = accounts[0];
                    await setupWalletProvider();
                    updateWalletUI(true);
                }
            } catch (e) {}
        }
        async function setupWalletProvider() {
            walletProvider = new ethers.BrowserProvider(window.ethereum);
            signer = await walletProvider.getSigner();
            registryWrite = new ethers.Contract(REGISTRY_ADDRESS, REGISTRY_ABI, signer);
            resolverWrite = new ethers.Contract(RESOLVER_ADDRESS, RESOLVER_ABI, signer);
            pricingWrite = new ethers.Contract(PRICING_ADDRESS, PRICING_ABI, signer);
        }
        async function connectWallet() {
            if (typeof window.ethereum === 'undefined') {
                showAlert('registerAlert', 'danger', 'Please install MetaMask to use this feature');
                return;
            }
            try {
                const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                currentAccount = accounts[0];
                const chainId = await window.ethereum.request({ method: 'eth_chainId' });
                if (chainId !== POLYGON_CHAIN_ID_HEX) {
                    try {
                        await window.ethereum.request({
                            method: 'wallet_switchEthereumChain',
                            params: [{ chainId: POLYGON_CHAIN_ID_HEX }]
                        });
                    } catch (switchError) {
                        if (switchError.code === 4902) {
                            await window.ethereum.request({
                                method: 'wallet_addEthereumChain',
                                params: [{
                                    chainId: POLYGON_CHAIN_ID_HEX,
                                    chainName: 'Polygon Mainnet',
                                    nativeCurrency: { name: 'MATIC', symbol: 'MATIC', decimals: 18 },
                                    rpcUrls: ['https://polygon-rpc.com'],
                                    blockExplorerUrls: ['https://polygonscan.com/']
                                }]
                            });
                        } else {
                            throw switchError;
                        }
                    }
                }
                await setupWalletProvider();
                updateWalletUI(true);
                showAlert('registerAlert', 'success', 'Wallet connected successfully');
            } catch (error) {
                showAlert('registerAlert', 'danger', 'Failed to connect wallet: ' + (error.message || 'Unknown error'));
            }
        }
        function updateWalletUI(connected) {
            const statusIndicator = document.getElementById('statusIndicator');
            const walletStatus = document.getElementById('walletStatus');
            const connectBtn = document.getElementById('connectWalletBtn');
            if (connected && currentAccount) {
                statusIndicator.classList.add('connected');
                walletStatus.textContent = `Connected: ${currentAccount.substring(0, 6)}...${currentAccount.substring(38)}`;
                connectBtn.textContent = 'Connected ✓';
                connectBtn.disabled = true;
            } else {
                statusIndicator.classList.remove('connected');
                walletStatus.textContent = 'Not Connected';
                connectBtn.textContent = 'Connect Wallet';
                connectBtn.disabled = false;
            }
        }
        function switchTab(tabName, btn) {
            const tabs = document.querySelectorAll('.tab');
            const panes = document.querySelectorAll('.tab-pane');
            tabs.forEach(t => t.classList.remove('active'));
            panes.forEach(p => p.classList.remove('active'));
            if (btn) btn.classList.add('active');
            const pane = document.getElementById(tabName);
            if (pane) pane.classList.add('active');
        }
        function showAlert(elementId, type, message) {
            const alert = document.getElementById(elementId);
            alert.className = `alert alert-${type} show`;
            alert.textContent = message;
            setTimeout(() => {
                alert.classList.remove('show');
            }, 5000);
        }
        function generateNode(domainName) {
            return ethers.keccak256(ethers.toUtf8Bytes(domainName.toLowerCase()));
        }
        function isValidDomainName(name) {
            if (!name || name.length === 0 || name.length > 64) return false;
            const validPattern = /^[a-z0-9]+(-[a-z0-9]+)*$/;
            return validPattern.test(name.toLowerCase());
        }
        function isReservedName(name) {
            const reserved = ['admin', 'ucns', 'root', 'system'];
            return reserved.includes(name.toLowerCase());
        }
        function formatAddress(address) {
            if (!address || address === '0x0000000000000000000000000000000000000000') {
                return 'Not Set';
            }
            return `${address.substring(0, 6)}...${address.substring(38)}`;
        }
        function formatDate(timestamp) {
            if (!timestamp) return 'N/A';
            const ts = Number(timestamp);
            if (!ts) return 'N/A';
            return new Date(ts * 1000).toLocaleString();
        }
        async function whoisLookup() {
            const domainInput = document.getElementById('whoisDomain');
            const domain = domainInput.value.trim().toLowerCase();
            if (!domain) {
                showAlert('whoisAlert', 'danger', 'Please enter a domain name');
                document.getElementById('whoisResult').innerHTML = '';
                return;
            }
            if (!isValidDomainName(domain)) {
                showAlert('whoisAlert', 'danger', 'Invalid domain name format. Use only letters, numbers, and hyphens (not at start/end)');
                document.getElementById('whoisResult').innerHTML = '';
                return;
            }
            try {
                const available = await registryRead.isDomainAvailable(domain);
                if (available) {
                    const html = `
                        <div class="result-card">
                            <h3>${domain}.ucns</h3>
                            <div class="info-row">
                                <span class="info-label">Status:</span>
                                <span class="info-value">
                                    <span class="status-badge status-available">
                                        Available for Registration
                                    </span>
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Info:</span>
                                <span class="info-value">This domain is not registered yet. You can register it in the "Register Domain" tab.</span>
                            </div>
                        </div>
                    `;
                    document.getElementById('whoisResult').innerHTML = html;
                    showAlert('whoisAlert', 'info', 'Domain is available for registration');
                    return;
                }
                const info = await registryRead.getDomainInfo(domain);
                const node = generateNode(domain);
                const metadata = await resolverRead.getMetadata(node);
                let html = `
                    <div class="result-card">
                        <h3>${domain}.ucns</h3>
                        <div class="info-row">
                            <span class="info-label">Owner:</span>
                            <span class="info-value">${info.owner}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Registration Date:</span>
                            <span class="info-value">${formatDate(info.registrationDate)}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Expiration Date:</span>
                            <span class="info-value">${formatDate(info.expirationDate)}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span class="info-value">
                                <span class="status-badge ${info.isExpired ? 'status-expired' : 'status-taken'}">
                                    ${info.isExpired ? 'Expired' : 'Active'}
                                </span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Resolver:</span>
                            <span class="info-value">${formatAddress(info.resolver)}</span>
                        </div>
                `;
                if (metadata._addr && metadata._addr !== '0x0000000000000000000000000000000000000000') {
                    html += `
                        <div class="info-row">
                            <span class="info-label">Linked Address:</span>
                            <span class="info-value">${formatAddress(metadata._addr)}</span>
                        </div>
                    `;
                }
                if (metadata._email) {
                    html += `
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value">${metadata._email}</span>
                        </div>
                    `;
                }
                if (metadata._url) {
                    html += `
                        <div class="info-row">
                            <span class="info-label">Website:</span>
                            <span class="info-value"><a href="${metadata._url}" target="_blank">${metadata._url}</a></span>
                        </div>
                    `;
                }
                if (metadata._twitter) {
                    html += `
                        <div class="info-row">
                            <span class="info-label">Twitter:</span>
                            <span class="info-value">${metadata._twitter}</span>
                        </div>
                    `;
                }
                if (metadata._github) {
                    html += `
                        <div class="info-row">
                            <span class="info-label">GitHub:</span>
                            <span class="info-value">${metadata._github}</span>
                        </div>
                    `;
                }
                if (metadata._description) {
                    html += `
                        <div class="info-row">
                            <span class="info-label">Description:</span>
                            <span class="info-value">${metadata._description}</span>
                        </div>
                    `;
                }
                html += `</div>`;
                document.getElementById('whoisResult').innerHTML = html;
                showAlert('whoisAlert', 'success', 'Domain information retrieved successfully');
            } catch (error) {
                document.getElementById('whoisResult').innerHTML = '';
                showAlert('whoisAlert', 'danger', 'Error retrieving domain information: ' + (error.message || 'Unknown error'));
            }
        }
        async function checkAvailability() {
            const domain = document.getElementById('availabilityDomain').value.trim().toLowerCase();
            if (!domain) {
                showAlert('availabilityAlert', 'danger', 'Please enter a domain name');
                document.getElementById('availabilityResult').innerHTML = '';
                return;
            }
            if (!isValidDomainName(domain)) {
                showAlert('availabilityAlert', 'danger', 'Invalid domain name. Use only letters, numbers, and hyphens (not at start/end)');
                document.getElementById('availabilityResult').innerHTML = '';
                return;
            }
            if (isReservedName(domain)) {
                const html = `
                    <div class="result-card">
                        <h3>${domain}.ucns</h3>
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span class="info-value">
                                <span class="status-badge status-taken">
                                    ✗ Reserved Name
                                </span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Info:</span>
                            <span class="info-value">This name is reserved by the system and cannot be registered.</span>
                        </div>
                    </div>
                `;
                document.getElementById('availabilityResult').innerHTML = html;
                showAlert('availabilityAlert', 'danger', 'This is a reserved domain name');
                return;
            }
            try {
                const available = await registryRead.isDomainAvailable(domain);
                const price = await registryRead.getRegistrationPrice(domain, 1);
                const priceInMatic = ethers.formatEther(price);
                const html = `
                    <div class="result-card">
                        <h3>${domain}.ucns</h3>
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span class="info-value">
                                <span class="status-badge ${available ? 'status-available' : 'status-taken'}">
                                    ${available ? '✓ Available' : '✗ Not Available'}
                                </span>
                            </span>
                        </div>
                        ${available ? `
                        <div class="info-row">
                            <span class="info-label">Registration Price (1 year):</span>
                            <span class="info-value">${priceInMatic} MATIC</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Domain Length:</span>
                            <span class="info-value">${domain.length} characters</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Action:</span>
                            <span class="info-value">Go to "Register Domain" tab to register this domain</span>
                        </div>
                        ` : `
                        <div class="info-row">
                            <span class="info-label">Info:</span>
                            <span class="info-value">This domain is already registered. Try searching for it in the WHOIS tab.</span>
                        </div>
                        `}
                    </div>
                `;
                document.getElementById('availabilityResult').innerHTML = html;
                showAlert('availabilityAlert', available ? 'success' : 'info', available ? 'Domain is available for registration' : 'Domain is already registered');
            } catch (error) {
                document.getElementById('availabilityResult').innerHTML = '';
                showAlert('availabilityAlert', 'danger', 'Error checking availability: ' + (error.message || 'Unknown error'));
            }
        }
        function selectDuration(years, el) {
            document.querySelectorAll('.duration-btn').forEach(btn => btn.classList.remove('selected'));
            if (el) el.classList.add('selected');
            document.getElementById('registrationDuration').value = String(years);
        }
        function prefillRegistration(domain, duration) {
            document.getElementById('registerDomain').value = domain;
            document.getElementById('registrationDuration').value = String(duration);
            document.querySelectorAll('.duration-btn').forEach(btn => {
                const y = parseInt(btn.getAttribute('data-years'));
                btn.classList.toggle('selected', y === duration);
            });
            const tabBtn = document.querySelector('.tab[data-tab="register"]');
            switchTab('register', tabBtn);
        }
        async function registerDomain() {
            if (!currentAccount || !registryWrite) {
                showAlert('registerAlert', 'danger', 'Please connect your wallet first');
                return;
            }
            const domain = document.getElementById('registerDomain').value.trim().toLowerCase();
            const duration = parseInt(document.getElementById('registrationDuration').value);
            if (!domain) {
                showAlert('registerAlert', 'danger', 'Please enter a domain name');
                return;
            }
            if (!isValidDomainName(domain)) {
                showAlert('registerAlert', 'danger', 'Invalid domain name format. Use only letters, numbers, and hyphens (not at start/end). Max 64 characters.');
                return;
            }
            if (isReservedName(domain)) {
                showAlert('registerAlert', 'danger', 'This is a reserved domain name and cannot be registered.');
                return;
            }
            if (isNaN(duration) || duration < 1 || duration > 10) {
                showAlert('registerAlert', 'danger', 'Duration must be between 1 and 10 years');
                return;
            }
            try {
                showAlert('registerAlert', 'info', 'Checking domain availability...');
                const available = await registryRead.isDomainAvailable(domain);
                if (!available) {
                    showAlert('registerAlert', 'danger', 'Domain is not available. It may already be registered.');
                    return;
                }
                const price = await registryRead.getRegistrationPrice(domain, duration);
                const priceInMatic = ethers.formatEther(price);
                const balance = await walletProvider.getBalance(currentAccount);
                const balanceInMatic = ethers.formatEther(balance);
                if (Number(balanceInMatic) < Number(priceInMatic)) {
                    showAlert('registerAlert', 'danger', `Insufficient MATIC balance. You need ${priceInMatic} MATIC but have ${Number(balanceInMatic).toFixed(4)} MATIC`);
                    return;
                }
                const confirmMsg = `Register ${domain}.ucns for ${duration} year(s)?\n\nPrice: ${priceInMatic} MATIC\nYour Balance: ${Number(balanceInMatic).toFixed(4)} MATIC\n\nClick OK to proceed.`;
                if (!window.confirm(confirmMsg)) return;
                showAlert('registerAlert', 'info', 'Processing transaction... Please confirm in MetaMask and wait for confirmation.');
                const tx = await registryWrite.registerDomain(domain, duration, { value: price });
                const receipt = await tx.wait();
                const html = `
                    <div class="result-card">
                        <h3>✓ Registration Successful!</h3>
                        <div class="info-row">
                            <span class="info-label">Domain:</span>
                            <span class="info-value">${domain}.ucns</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Duration:</span>
                            <span class="info-value">${duration} year(s)</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Price Paid:</span>
                            <span class="info-value">${priceInMatic} MATIC</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Transaction Hash:</span>
                            <span class="info-value">
                                <a href="https://polygonscan.com/tx/${receipt.hash}" target="_blank" style="color: #667eea; text-decoration: underline;">
                                    ${receipt.hash.substring(0, 10)}...${receipt.hash.substring(56)}
                                </a>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Block Number:</span>
                            <span class="info-value">${receipt.blockNumber}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Next Steps:</span>
                            <span class="info-value">Go to "Manage Metadata" tab to set up your domain information</span>
                        </div>
                    </div>
                `;
                document.getElementById('registerResult').innerHTML = html;
                showAlert('registerAlert', 'success', 'Domain registered successfully');
                document.getElementById('registerDomain').value = '';
            } catch (error) {
                let msg = 'Registration failed: ';
                const text = error.message || '';
                if (text.includes('User denied') || text.includes('rejected')) {
                    msg = 'Transaction was rejected by user';
                } else if (text.includes('insufficient funds')) {
                    msg = 'Insufficient MATIC balance for transaction';
                } else if (text.includes('already registered')) {
                    msg = 'Domain is already registered';
                } else if (text.includes('Invalid domain name')) {
                    msg = 'Invalid domain name format';
                } else {
                    msg += text || 'Unknown error';
                }
                showAlert('registerAlert', 'danger', msg);
                document.getElementById('registerResult').innerHTML = '';
            }
        }
        async function loadDomainMetadata() {
            if (!currentAccount || !registryRead) {
                showAlert('metadataAlert', 'danger', 'Please connect your wallet first');
                return;
            }
            const domain = document.getElementById('metadataDomain').value.trim().toLowerCase();
            if (!domain) {
                showAlert('metadataAlert', 'danger', 'Please enter a domain name');
                document.getElementById('metadataForm').style.display = 'none';
                return;
            }
            if (!isValidDomainName(domain)) {
                showAlert('metadataAlert', 'danger', 'Invalid domain name format');
                document.getElementById('metadataForm').style.display = 'none';
                return;
            }
            try {
                showAlert('metadataAlert', 'info', 'Loading domain information...');
                const available = await registryRead.isDomainAvailable(domain);
                if (available) {
                    showAlert('metadataAlert', 'danger', 'This domain is not registered yet');
                    document.getElementById('metadataForm').style.display = 'none';
                    return;
                }
                const info = await registryRead.getDomainInfo(domain);
                if (info.owner.toLowerCase() !== currentAccount.toLowerCase()) {
                    showAlert('metadataAlert', 'danger', `You do not own this domain. Owner: ${formatAddress(info.owner)}`);
                    document.getElementById('metadataForm').style.display = 'none';
                    return;
                }
                if (info.isExpired) {
                    showAlert('metadataAlert', 'danger', 'This domain has expired. You cannot update metadata');
                    document.getElementById('metadataForm').style.display = 'none';
                    return;
                }
                const node = generateNode(domain);
                const metadata = await resolverRead.getMetadata(node);
                document.getElementById('metaAddress').value = metadata._addr && metadata._addr !== '0x0000000000000000000000000000000000000000' ? metadata._addr : '';
                document.getElementById('metaEmail').value = metadata._email || '';
                document.getElementById('metaAvatar').value = metadata._avatar || '';
                document.getElementById('metaURL').value = metadata._url || '';
                document.getElementById('metaTwitter').value = metadata._twitter || '';
                document.getElementById('metaGithub').value = metadata._github || '';
                document.getElementById('metaDescription').value = metadata._description || '';
                document.getElementById('metadataForm').style.display = 'block';
                showAlert('metadataAlert', 'success', 'Metadata loaded successfully');
            } catch (error) {
                document.getElementById('metadataForm').style.display = 'none';
                showAlert('metadataAlert', 'danger', 'Error loading metadata: ' + (error.message || 'Unknown error'));
            }
        }
        async function updateBulkMetadata() {
            if (!currentAccount || !resolverWrite) {
                showAlert('metadataAlert', 'danger', 'Please connect your wallet first');
                return;
            }
            const domain = document.getElementById('metadataDomain').value.trim().toLowerCase();
            if (!domain) {
                showAlert('metadataAlert', 'danger', 'Please enter a domain name');
                return;
            }
            if (!isValidDomainName(domain)) {
                showAlert('metadataAlert', 'danger', 'Invalid domain name format');
                return;
            }
            const node = generateNode(domain);
            let addr = document.getElementById('metaAddress').value.trim();
            const email = document.getElementById('metaEmail').value.trim();
            const avatar = document.getElementById('metaAvatar').value.trim();
            const description = document.getElementById('metaDescription').value.trim();
            const url = document.getElementById('metaURL').value.trim();
            const twitter = document.getElementById('metaTwitter').value.trim();
            const github = document.getElementById('metaGithub').value.trim();
            if (addr && !ethers.isAddress(addr)) {
                showAlert('metadataAlert', 'danger', 'Invalid Ethereum address format');
                return;
            }
            if (!addr) {
                addr = '0x0000000000000000000000000000000000000000';
            }
            try {
                showAlert('metadataAlert', 'info', 'Processing metadata update transactions...');
                let txCount = 0;
                if (addr !== '0x0000000000000000000000000000000000000000' || email || avatar || description) {
                    const tx = await resolverWrite.setBulkMetadata(node, addr, email, avatar, description);
                    await tx.wait();
                    txCount++;
                }
                if (url) {
                    const tx2 = await resolverWrite.setURL(node, url);
                    await tx2.wait();
                    txCount++;
                }
                if (twitter) {
                    const tx3 = await resolverWrite.setTwitter(node, twitter);
                    await tx3.wait();
                    txCount++;
                }
                if (github) {
                    const tx4 = await resolverWrite.setGithub(node, github);
                    await tx4.wait();
                    txCount++;
                }
                showAlert('metadataAlert', 'success', `Metadata updated successfully. ${txCount} transaction(s) completed`);
                setTimeout(() => loadDomainMetadata(), 2000);
            } catch (error) {
                let msg = 'Update failed: ';
                const text = error.message || '';
                if (text.includes('User denied') || text.includes('rejected')) {
                    msg = 'Transaction was rejected by user';
                } else if (text.includes('Only domain owner')) {
                    msg = 'Only the domain owner can update metadata';
                } else if (text.includes('Domain expired')) {
                    msg = 'Cannot update metadata for expired domains';
                } else if (text.includes('insufficient funds')) {
                    msg = 'Insufficient MATIC for transaction fees';
                } else {
                    msg += text || 'Unknown error';
                }
                showAlert('metadataAlert', 'danger', msg);
            }
        }
        async function calculatePrice() {
            const domain = document.getElementById('pricingDomain').value.trim().toLowerCase();
            const duration = parseInt(document.getElementById('pricingDuration').value);
            if (!domain) {
                showAlert('pricingAlert', 'danger', 'Please enter a domain name');
                document.getElementById('pricingResult').innerHTML = '';
                return;
            }
            if (!isValidDomainName(domain)) {
                showAlert('pricingAlert', 'danger', 'Invalid domain name format. Use only letters, numbers, and hyphens.');
                document.getElementById('pricingResult').innerHTML = '';
                return;
            }
            if (isNaN(duration) || duration < 1 || duration > 10) {
                showAlert('pricingAlert', 'danger', 'Duration must be between 1 and 10 years');
                document.getElementById('pricingResult').innerHTML = '';
                return;
            }
            try {
                const totalPrice = await registryRead.getRegistrationPrice(domain, duration);
                const basePrice = await pricingRead.calculatePrice(domain.length);
                const priceInMatic = ethers.formatEther(totalPrice);
                const basePriceInMatic = ethers.formatEther(basePrice);
                const approxUsd = (Number(priceInMatic) * 0.5).toFixed(2);
                const available = await registryRead.isDomainAvailable(domain);
                const html = `
                    <div class="price-calculator">
                        <div class="price-card">
                            <div class="price-label">Total Price</div>
                            <div class="price-amount">${priceInMatic} MATIC</div>
                            <div class="price-label">≈ $${approxUsd} USD</div>
                            <div class="price-label">for ${duration} year(s)</div>
                        </div>
                        <div class="price-card">
                            <div class="price-label">Price Per Year</div>
                            <div class="price-amount">${basePriceInMatic} MATIC</div>
                            <div class="price-label">${domain.length} character domain</div>
                        </div>
                    </div>
                    <div class="result-card" style="margin-top: 20px;">
                        <h3>Pricing Details</h3>
                        <div class="info-row">
                            <span class="info-label">Domain Name:</span>
                            <span class="info-value">${domain}.ucns</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Availability:</span>
                            <span class="info-value">
                                <span class="status-badge ${available ? 'status-available' : 'status-taken'}">
                                    ${available ? '✓ Available' : '✗ Not Available'}
                                </span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Domain Length:</span>
                            <span class="info-value">${domain.length} characters</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Duration:</span>
                            <span class="info-value">${duration} year(s)</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Base Price:</span>
                            <span class="info-value">${basePriceInMatic} MATIC/year</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Total Cost:</span>
                            <span class="info-value"><strong>${priceInMatic} MATIC</strong></span>
                        </div>
                        ${available ? `
                        <div class="info-row" style="border: none; padding-top: 20px;">
                            <span class="info-value" style="width: 100%; text-align: center;">
                                <button class="btn btn-success" onclick="prefillRegistration('${domain}', ${duration})">
                                    Register This Domain Now
                                </button>
                            </span>
                        </div>
                        ` : ''}
                    </div>
                `;
                document.getElementById('pricingResult').innerHTML = html;
                showAlert('pricingAlert', 'success', 'Price calculated successfully');
            } catch (error) {
                document.getElementById('pricingResult').innerHTML = '';
                showAlert('pricingAlert', 'danger', 'Error calculating price: ' + (error.message || 'Unknown error'));
            }
        }
        if (window.ethereum) {
            window.ethereum.on('accountsChanged', function (accounts) {
                if (!accounts || accounts.length === 0) {
                    currentAccount = null;
                    updateWalletUI(false);
                } else {
                    currentAccount = accounts[0];
                    setupWalletProvider().then(() => updateWalletUI(true));
                }
            });
            window.ethereum.on('chainChanged', function () {
                window.location.reload();
            });
        }
    </script>
</body>
</html>