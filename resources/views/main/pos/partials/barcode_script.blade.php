<script>
    // ==================== BARCODE SCANNER HANDLER ====================
    let barcodeBuffer = '';
    let barcodeTimeout;

    document.addEventListener('keydown', function(e) {
        const target = e.target;
        
        // 1. Handle Input in Search Box specifically
        if (target.id === 'searchProduct' && e.key === 'Enter') {
            // We only prevent default if we successfully handled it as a scanner input
            // But usually scanner sends Enter at the end.
            // We'll check if it matches a product.
            handleSearchProductEnter(target, e);
            return;
        }

        // 2. Ignore other inputs (modals, quantity, etc)
        if (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target.isContentEditable) {
            return;
        }

        // 3. Global Listener (when no input is focused)
        if (e.key === 'Enter') {
            if (barcodeBuffer.length >= 2) { // Minimum length to prevent accidental enters
                if(handleBarcodeScan(barcodeBuffer)) {
                    // Success
                } else {
                    // If not found global, maybe show warning?
                    // showToast('warning', 'Barcode tidak ditemukan: ' + barcodeBuffer);
                }
            }
            // Always clear buffer on Enter
            barcodeBuffer = '';
            return;
        }

        // Ignore excessive keys (Ctrl, Alt, etc)
        if (e.key.length > 1) return;

        // Build Buffer
        barcodeBuffer += e.key;

        // Reset buffer if gap is too long (manual typing vs scanner)
        clearTimeout(barcodeTimeout);
        barcodeTimeout = setTimeout(() => {
            barcodeBuffer = '';
        }, 100); // 100ms gap reset
    });

    function handleSearchProductEnter(input, event) {
        const code = input.value.trim();
        if (!code) return;

        // Try to find product by exact code/barcode match
        // We use the boolean return to decide if we consume the event
        if (handleBarcodeScan(code)) {
            event.preventDefault(); // Prevent form submit or other enter actions
            input.value = ''; // Clear input
            filterProducts('', ''); // Reset filter to show all
            input.focus(); // Keep focus
        } 
        // If not found, let natural behavior happen (it remains as a search term filter)
    }

    function handleBarcodeScan(code) {
        // Cleaning code (some scanners add checksums or hidden chars, but usually just trim is enough)
        code = code.trim();

        // Search in DOM for product match
        // Priority: Barcode -> Code
        let el = document.querySelector(`.product-card[data-product-barcode="${code}"]`);
        if (!el) {
            // Fallback to Product Code
            el = document.querySelector(`.product-card[data-product-code="${code}"]`);
        }

        if (el) {
            addProductToCart(el);
            // Optional: Sound effect
            // playBeep(); 
            return true;
        }

        return false;
    }
</script>
