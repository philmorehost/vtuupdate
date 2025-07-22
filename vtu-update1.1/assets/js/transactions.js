window.showTransactionDetailsModal = function(transactionId) {
    fetch(`api/transaction-details.php?id=${transactionId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(response => {
            if (response.success) {
                const transaction = response.data;
                const transactionDetailsContent = document.getElementById('transaction-details-content');
                const printReceiptBtn = document.getElementById('print-receipt-btn');
                const transactionDetailsModal = document.getElementById('transaction-details-modal');
                let detailsHtml = `
                    <p class="mb-2"><span class="font-semibold">Transaction ID:</span> ${transaction.id}</p>
                    <p class="mb-2"><span class="font-semibold">Type:</span> ${transaction.type}</p>
                    <p class="mb-2"><span class="font-semibold">Description:</span> ${transaction.description}</p>
                    <p class="mb-2"><span class="font-semibold">Amount:</span> <span class="${transaction.amount < 0 ? 'text-red-600' : 'text-green-600'}">${transaction.amount < 0 ? '-' : '+'}₦${Math.abs(transaction.amount).toFixed(2)}</span></p>
                    <p class="mb-2"><span class="font-semibold">Date:</span> ${new Date(transaction.date).toLocaleString()}</p>
                    <p class="mb-4"><span class="font-semibold">Status:</span> ${transaction.status}</p>
                    <p class="mb-2"><span class="font-semibold">Balance Before:</span> ₦${Number(transaction.balance_before).toFixed(2)}</p>
                    <p class="mb-2"><span class="font-semibold">Balance After:</span> ₦${Number(transaction.balance_after).toFixed(2)}</p>
                `;

                if (transaction.serviceDetails) {
                    detailsHtml += `<h4 class="font-semibold text-lg mb-2">Service Details:</h4><ul>`;
                    for (const key in transaction.serviceDetails) {
                        detailsHtml += `<li class="mb-1"><span class="font-medium">${key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase())}:</span> ${transaction.serviceDetails[key]}</li>`;
                    }
                    detailsHtml += `</ul>`;
                }

                transactionDetailsContent.innerHTML = detailsHtml;
                printReceiptBtn.dataset.transactionId = transactionId;
                transactionDetailsModal.classList.remove('hidden');
            } else {
                alert(response.message);
            }
        })
        .catch(error => {
            console.error('Error fetching transaction details:', error);
            alert('An error occurred while fetching transaction details. Please check the console for more information.');
        });
}

window.printTransactionReceipt = function(transaction) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Fetch logo
    fetch('api/get_logo.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.logo_path) {
                const img = new Image();
                img.src = data.logo_path;
                img.onload = () => {
                    doc.addImage(img, 'PNG', 10, 10, 30, 30);
                    generateReceipt(doc, transaction);
                };
            } else {
                generateReceipt(doc, transaction);
            }
        })
        .catch(() => {
            generateReceipt(doc, transaction);
        });
};

function generateReceipt(doc, transaction) {
    doc.setFontSize(22);
    doc.text("Transaction Receipt", 105, 20, null, null, "center");

    doc.setFontSize(12);
    doc.text(`Date: ${new Date().toLocaleString()}`, 105, 30, null, null, "center");
    doc.line(20, 35, 190, 35);

    let y = 45;
    doc.setFontSize(14);
    doc.text("Transaction Details:", 20, y);
    y += 10;
    doc.setFontSize(12);
    doc.text(`Transaction ID: ${transaction.id}`, 20, y); y += 7;
    doc.text(`Type: ${transaction.type}`, 20, y); y += 7;
    doc.text(`Description: ${transaction.description}`, 20, y); y += 7;
    doc.text(`Amount: ${Math.abs(transaction.amount).toFixed(2)}₦`, 20, y); y += 7;
    doc.text(`Status: ${transaction.status}`, 20, y); y += 7;
    doc.text(`Transaction Date: ${new Date(transaction.date).toLocaleString()}`, 20, y); y += 10;

    if (transaction.serviceDetails) {
        doc.setFontSize(14);
        doc.text("Service Specifics:", 20, y);
        y += 10;
        doc.setFontSize(12);
        for (const key in transaction.serviceDetails) {
            doc.text(`${key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase())}: ${transaction.serviceDetails[key]}`, 20, y);
            y += 7;
        }
    }

    doc.line(20, y + 5, 190, y + 5);
    doc.setFontSize(10);
    doc.text("Thank you for your business!", 105, y + 15, null, null, "center");

    doc.save(`receipt_${transaction.id}.pdf`);
    alert('Receipt printed successfully!');
}
