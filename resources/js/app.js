import "./bootstrap";

// Tunggu sampai DOM siap dan Echo tersedia
document.addEventListener("DOMContentLoaded", async function () {
    const userIdMeta = document.querySelector('meta[name="user-id"]');
    console.log("[app] userId meta?", userIdMeta?.content);

    if (!userIdMeta) {
        console.warn("[app] user-id meta not found");
        return;
    }

    // Pastikan notifikasi container sudah dibuat
    ensureNotificationContainer();

    // Tunggu sampai Echo siap
    try {
        const echoReady = await window.echoReady;

        if (!echoReady || !window.Echo) {
            console.error("[app] Echo failed to initialize");
            return;
        }

        const userId = userIdMeta.content;
        const channelName = "user." + userId;

        console.log("[app] Subscribing to channel:", channelName);

        // Subscribe ke channel
        window.Echo.channel(channelName)
            .listen(".delivery.status.updated", (data) => {
                console.log("[Echo] ✅ Event received!", data);

                // Tampilkan notifikasi dengan data dari event
                showNotification(
                    `Status Pengantaran ${capitalizeFirstLetter(data.shift)}`,
                    data.message,
                    getNotificationType(data.status)
                );

                // Update UI status pengiriman di halaman
                updateDeliveryStatus(data);
            })
            .error((error) => {
                console.error("[Echo] ❌ Channel subscription error:", error);
            });

        console.log(
            "[app] ✅ Successfully subscribed to channel:",
            channelName
        );
    } catch (error) {
        console.error("[app] Error initializing Echo listener:", error);
    }
});

// Buat container notifikasi
function ensureNotificationContainer() {
    let notifBox = document.getElementById("notif-box");
    if (!notifBox) {
        notifBox = document.createElement("div");
        notifBox.id = "notif-box";
        notifBox.className = "position-fixed top-0 end-0 p-3";
        notifBox.style.cssText = `
            z-index: 2000; 
            min-width: 300px; 
            max-width: 500px;
            max-height: 100vh;
            overflow-y: auto;
        `;
        document.body.appendChild(notifBox);
    }
    return notifBox;
}

// Helper function untuk capitalize
function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

// Fungsi untuk menentukan jenis notifikasi berdasarkan status
function getNotificationType(status) {
    const statusMap = {
        pending: "info",
        diproses: "warning",
        "sedang dikirim": "info",
        sampai: "success",
        "gagal dikirim": "error",
    };
    return statusMap[status] || "info";
}

// Fungsi untuk update UI status pengiriman berdasarkan shift dan status
function updateDeliveryStatus(data) {
    console.log(`[UI] Updating delivery status:`, data);

    const { shift, status, order_id } = data;

    // Update status berdasarkan shift (siang/malam)
    const shiftElement = document.querySelector(
        `[data-delivery-shift="${shift}"]`
    );
    if (shiftElement) {
        // Update status text
        const statusElement = shiftElement.querySelector(".delivery-status");
        if (statusElement) {
            statusElement.textContent = `Status: ${getStatusText(status)}`;
        }

        // Update visual indicator
        updateProgressIndicator(shiftElement, status);
    }

    // Highlight status yang aktif di tabel
    highlightActiveStatus(shift, status);
}

// Helper function untuk teks status yang lebih user-friendly
function getStatusText(status) {
    const statusTextMap = {
        pending: "🕒 Menunggu",
        diproses: "👨‍🍳 Diproses",
        "sedang dikirim": "🚚 Sedang Dikirim",
        sampai: "✅ Sampai",
        "gagal dikirim": "❌ Gagal Dikirim",
    };
    return statusTextMap[status] || capitalizeFirstLetter(status);
}

// Update progress indicator visual
function updateProgressIndicator(container, status) {
    // Reset semua status
    const steps = container.querySelectorAll("[data-status-step]");
    steps.forEach((step) => {
        step.classList.remove("active", "completed", "failed");
    });

    // Set status berdasarkan progress
    switch (status) {
        case "pending":
            container
                .querySelector('[data-status-step="pending"]')
                ?.classList.add("active");
            break;
        case "diproses":
            container
                .querySelector('[data-status-step="pending"]')
                ?.classList.add("completed");
            container
                .querySelector('[data-status-step="diproses"]')
                ?.classList.add("active");
            break;
        case "sedang dikirim":
            container
                .querySelector('[data-status-step="pending"]')
                ?.classList.add("completed");
            container
                .querySelector('[data-status-step="diproses"]')
                ?.classList.add("completed");
            container
                .querySelector('[data-status-step="sedang-dikirim"]')
                ?.classList.add("active");
            break;
        case "sampai":
            container
                .querySelector('[data-status-step="pending"]')
                ?.classList.add("completed");
            container
                .querySelector('[data-status-step="diproses"]')
                ?.classList.add("completed");
            container
                .querySelector('[data-status-step="sedang-dikirim"]')
                ?.classList.add("completed");
            container
                .querySelector('[data-status-step="sampai"]')
                ?.classList.add("active");
            break;
        case "gagal dikirim":
            container
                .querySelector('[data-status-step="gagal"]')
                ?.classList.add("active", "failed");
            break;
    }
}

// Highlight status aktif di tabel
function highlightActiveStatus(shift, status) {
    const statusMap = {
        pending: "PENDING",
        diproses: "PROSES",
        "sedang dikirim": "KIRIM",
        sampai: "SAMPAI",
        "gagal dikirim": "GAGAL",
    };

    const tableStatus = statusMap[status];
    if (!tableStatus) return;

    // Cari tabel untuk shift yang sesuai
    const shiftTables = document.querySelectorAll(
        `[data-shift-table="${shift}"]`
    );

    shiftTables.forEach((table) => {
        // Reset semua status
        const allCells = table.querySelectorAll("td[data-status]");
        allCells.forEach((cell) => {
            cell.style.backgroundColor = "";
            cell.style.color = "";
            cell.style.fontWeight = "";
        });

        // Highlight status aktif
        const activeCell = table.querySelector(
            `td[data-status="${tableStatus}"]`
        );
        if (activeCell) {
            activeCell.style.backgroundColor = "#007bff";
            activeCell.style.color = "white";
            activeCell.style.fontWeight = "bold";
            activeCell.style.borderRadius = "4px";
        }
    });
}

// Fungsi untuk menampilkan notifikasi
function showNotification(title, message, type = "info") {
    let notifBox = document.getElementById("notif-box");

    if (!notifBox) {
        notifBox = ensureNotificationContainer();
    }

    const alertConfig = {
        success: {
            icon: "✅",
            bgColor: "#d1e7dd",
            textColor: "#0f5132",
        },
        error: {
            icon: "❌",
            bgColor: "#f8d7da",
            textColor: "#721c24",
        },
        warning: {
            icon: "⚠️",
            bgColor: "#fff3cd",
            textColor: "#856404",
        },
        info: {
            icon: "ℹ️",
            bgColor: "#cff4fc",
            textColor: "#055160",
        },
    };

    const config = alertConfig[type] || alertConfig.info;

    const notification = document.createElement("div");
    notification.style.cssText = `
        background-color: ${config.bgColor};
        color: ${config.textColor};
        border: 1px solid transparent;
        padding: 12px 16px;
        margin-bottom: 10px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideInRight 0.3s ease-out;
    `;

    notification.innerHTML = `
        <div style="font-size: 18px; flex-shrink: 0;">${config.icon}</div>
        <div style="flex: 1;">
            <strong style="display: block; margin-bottom: 4px; font-size: 14px;">${title}</strong>
            <div style="font-size: 13px; opacity: 0.9;">${message}</div>
        </div>
        <button type="button" class="btn-close" style="flex-shrink: 0; margin-left: 10px;" onclick="this.parentElement.remove()"></button>
    `;

    notifBox.appendChild(notification);

    // Auto remove setelah 5 detik
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = "slideInRight 0.3s ease-out reverse";
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Export untuk global access
window.showNotification = showNotification;
