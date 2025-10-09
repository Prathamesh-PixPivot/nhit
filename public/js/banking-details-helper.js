/**
 * Banking Details Helper
 * Provides functionality for auto-populating banking details in forms
 */

class BankingDetailsHelper {
    constructor(options = {}) {
        this.baseUrl = options.baseUrl || '/api/backend';
        this.csrfToken = options.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.debug = options.debug || false;
    }

    /**
     * Auto-populate banking details based on form type and vendor
     */
    async autoPopulateBankingDetails(formType, vendorId = null, additionalParams = {}) {
        try {
            const params = new URLSearchParams({
                form_type: formType,
                ...additionalParams
            });

            if (vendorId) {
                params.append('vendor_id', vendorId);
            }

            const response = await fetch(`${this.baseUrl}/banking-details?${params}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                return data.data;
            } else {
                throw new Error(data.message || 'Failed to get banking details');
            }
        } catch (error) {
            this.log('Error getting banking details:', error);
            throw error;
        }
    }

    /**
     * Get vendor's all banking accounts
     */
    async getVendorAccounts(vendorId) {
        try {
            const response = await fetch(`${this.baseUrl}/vendor/${vendorId}/accounts`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                return data.data;
            } else {
                throw new Error(data.message || 'Failed to get vendor accounts');
            }
        } catch (error) {
            this.log('Error getting vendor accounts:', error);
            throw error;
        }
    }

    /**
     * Validate banking details
     */
    async validateBankingDetails(details) {
        try {
            const response = await fetch(`${this.baseUrl}/banking-details/validate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(details)
            });

            const data = await response.json();
            return data;
        } catch (error) {
            this.log('Error validating banking details:', error);
            throw error;
        }
    }

    /**
     * Get IFSC code details
     */
    async getIFSCDetails(ifscCode) {
        try {
            const response = await fetch(`${this.baseUrl}/banking-details/ifsc/${ifscCode}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                return data.data;
            } else {
                throw new Error(data.message || 'IFSC code not found');
            }
        } catch (error) {
            this.log('Error getting IFSC details:', error);
            throw error;
        }
    }

    /**
     * Populate form fields with banking details
     */
    populateFormFields(details, fieldMapping = {}) {
        const defaultMapping = {
            'account_name': ['beneficiary_name', 'account_holder_name', 'account_name'],
            'account_number': ['account_number', 'account_no'],
            'name_of_bank': ['bank_name', 'name_of_bank'],
            'ifsc_code': ['ifsc_code', 'ifsc'],
            'branch_name': ['branch_name', 'branch'],
            'swift_code': ['swift_code', 'swift']
        };

        const mapping = { ...defaultMapping, ...fieldMapping };

        Object.keys(details).forEach(key => {
            if (details[key] && mapping[key]) {
                mapping[key].forEach(fieldName => {
                    const field = document.querySelector(`[name="${fieldName}"], #${fieldName}`);
                    if (field) {
                        field.value = details[key];
                        // Trigger change event
                        field.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            }
        });
    }

    /**
     * Create vendor account dropdown
     */
    createAccountDropdown(accounts, containerId, options = {}) {
        const container = document.getElementById(containerId);
        if (!container) {
            this.log('Container not found:', containerId);
            return;
        }

        const select = document.createElement('select');
        select.className = options.className || 'form-select';
        select.name = options.name || 'vendor_account_id';
        select.id = options.id || 'vendor_account_select';

        // Add default option
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = options.placeholder || 'Select Account';
        select.appendChild(defaultOption);

        // Add account options
        accounts.all_accounts.forEach(account => {
            const option = document.createElement('option');
            option.value = account.id;
            option.textContent = `${account.account_number} - ${account.name_of_bank}`;
            if (account.is_primary) {
                option.textContent += ' (Primary)';
                option.selected = true;
            }
            option.dataset.accountDetails = JSON.stringify(account);
            select.appendChild(option);
        });

        // Add change event listener
        select.addEventListener('change', (e) => {
            if (e.target.value) {
                const accountDetails = JSON.parse(e.target.selectedOptions[0].dataset.accountDetails);
                this.populateFormFields(accountDetails, options.fieldMapping);
            }
        });

        container.innerHTML = '';
        container.appendChild(select);

        return select;
    }

    /**
     * Setup IFSC code auto-completion
     */
    setupIFSCAutoComplete(ifscFieldSelector, bankFieldSelector = null, branchFieldSelector = null) {
        const ifscField = document.querySelector(ifscFieldSelector);
        if (!ifscField) {
            this.log('IFSC field not found:', ifscFieldSelector);
            return;
        }

        let debounceTimer;

        ifscField.addEventListener('input', (e) => {
            clearTimeout(debounceTimer);
            const ifscCode = e.target.value.toUpperCase();
            e.target.value = ifscCode;

            if (ifscCode.length === 11) {
                debounceTimer = setTimeout(async () => {
                    try {
                        const details = await this.getIFSCDetails(ifscCode);
                        
                        if (bankFieldSelector) {
                            const bankField = document.querySelector(bankFieldSelector);
                            if (bankField && details.bank) {
                                bankField.value = details.bank;
                                bankField.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                        }

                        if (branchFieldSelector) {
                            const branchField = document.querySelector(branchFieldSelector);
                            if (branchField && details.branch) {
                                branchField.value = details.branch;
                                branchField.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                        }

                        // Show success indicator
                        this.showFieldStatus(ifscField, 'success', 'Valid IFSC code');

                    } catch (error) {
                        this.showFieldStatus(ifscField, 'error', 'Invalid IFSC code');
                    }
                }, 500);
            }
        });
    }

    /**
     * Show field validation status
     */
    showFieldStatus(field, status, message) {
        // Remove existing status
        field.classList.remove('is-valid', 'is-invalid');
        
        const existingFeedback = field.parentNode.querySelector('.valid-feedback, .invalid-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }

        // Add new status
        if (status === 'success') {
            field.classList.add('is-valid');
            const feedback = document.createElement('div');
            feedback.className = 'valid-feedback';
            feedback.textContent = message;
            field.parentNode.appendChild(feedback);
        } else if (status === 'error') {
            field.classList.add('is-invalid');
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = message;
            field.parentNode.appendChild(feedback);
        }
    }

    /**
     * Setup vendor change handler for auto-population
     */
    setupVendorChangeHandler(vendorSelectSelector, formType, options = {}) {
        const vendorSelect = document.querySelector(vendorSelectSelector);
        if (!vendorSelect) {
            this.log('Vendor select not found:', vendorSelectSelector);
            return;
        }

        vendorSelect.addEventListener('change', async (e) => {
            const vendorId = e.target.value;
            
            if (vendorId) {
                try {
                    // Show loading indicator
                    if (options.loadingCallback) {
                        options.loadingCallback(true);
                    }

                    const bankingDetails = await this.autoPopulateBankingDetails(formType, vendorId);
                    
                    if (bankingDetails && Object.keys(bankingDetails).length > 0) {
                        this.populateFormFields(bankingDetails, options.fieldMapping);
                        
                        if (options.successCallback) {
                            options.successCallback(bankingDetails);
                        }
                    }

                } catch (error) {
                    this.log('Error auto-populating banking details:', error);
                    
                    if (options.errorCallback) {
                        options.errorCallback(error);
                    }
                } finally {
                    if (options.loadingCallback) {
                        options.loadingCallback(false);
                    }
                }
            } else {
                // Clear fields when no vendor is selected
                this.clearFormFields(options.fieldMapping);
            }
        });
    }

    /**
     * Clear form fields
     */
    clearFormFields(fieldMapping = {}) {
        const defaultFields = [
            'beneficiary_name', 'account_holder_name', 'account_name',
            'account_number', 'account_no',
            'bank_name', 'name_of_bank',
            'ifsc_code', 'ifsc',
            'branch_name', 'branch',
            'swift_code', 'swift'
        ];

        const allFields = [...defaultFields];
        
        if (fieldMapping) {
            Object.values(fieldMapping).forEach(fields => {
                if (Array.isArray(fields)) {
                    allFields.push(...fields);
                }
            });
        }

        allFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"], #${fieldName}`);
            if (field) {
                field.value = '';
                field.classList.remove('is-valid', 'is-invalid');
            }
        });
    }

    /**
     * Log debug messages
     */
    log(...args) {
        if (this.debug) {
            console.log('[BankingDetailsHelper]', ...args);
        }
    }
}

// Make it globally available
window.BankingDetailsHelper = BankingDetailsHelper;
