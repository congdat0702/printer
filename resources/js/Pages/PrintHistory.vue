<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lịch sử In</h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-if="printHistories.data.length === 0" class="text-gray-500 col-span-full">Không có lịch sử in nào để hiển thị.</div>
            <div v-for="history in printHistories.data" :key="history.id" class="history-card" @click="openModal(history)" style="cursor:pointer;">
              <h4><i class="fas fa-user"></i> {{ history.name }}</h4>
              <p><i class="fas fa-phone"></i> {{ history.phone }}</p>
              <p><i class="fas fa-print"></i> In bởi: {{ history.printer }}</p>
              <p><i class="fas fa-copy"></i> Số trang in: {{ history.quantity || 1 }}</p>
              <p style="color: #ef4444; font-weight: 600;"><i class="fas fa-history"></i> Khách đã in: {{ history.total_prints }} lần</p>
              <p><i class="fas fa-clock"></i> {{ history.time }}</p>
            </div>
          </div>
          <div class="pagination">
            <button @click="prevPage" :disabled="printHistories.current_page === 1">Trước</button>
            <span>Trang {{ printHistories.current_page }} / {{ printHistories.last_page }}</span>
            <button @click="nextPage" :disabled="printHistories.current_page === printHistories.last_page">Sau</button>
          </div>
        </div>
      </div>
    </div>
    <Modal :show="showModal" @close="closeModal">
      <template v-if="selectedHistory">
        <div class="modal-detail-wrapper">
          <h3 class="modal-title">Chi tiết lịch sử in</h3>
          <div class="modal-detail-list">
            <div class="modal-detail-row"><span class="modal-label">Tên người nhận:</span><span class="modal-value">{{ selectedHistory.name }}</span></div>
            <div class="modal-detail-row"><span class="modal-label">SĐT người nhận:</span><span class="modal-value">{{ selectedHistory.phone }}</span></div>
            <div class="modal-detail-row"><span class="modal-label">Người gửi:</span><span class="modal-value">{{ selectedHistory.sender_name || '-' }}</span></div>
            <div class="modal-detail-row"><span class="modal-label">Đơn vị vận chuyển:</span><span class="modal-value">{{ selectedHistory.shipping_unit || '-' }}</span></div>
            <div class="modal-detail-row"><span class="modal-label">COD:</span><span class="modal-value">{{ selectedHistory.cod || '-' }}</span></div>
            <div class="modal-detail-row"><span class="modal-label">Tên hàng hóa:</span><span class="modal-value">{{ selectedHistory.product_name || '-' }}</span></div>
            <div class="modal-detail-row"><span class="modal-label">Người trả phí:</span><span class="modal-value">{{ selectedHistory.payer || '-' }}</span></div>
            <div class="modal-detail-row"><span class="modal-label">In bởi:</span><span class="modal-value">{{ selectedHistory.printer }}</span></div>
            <div class="modal-detail-row"><span class="modal-label">Số trang in (1 phiên):</span><span class="modal-value">{{ selectedHistory.quantity || 1 }}</span></div>
            <div class="modal-detail-row"><span class="modal-label">Tổng số lần in (lịch sử):</span><span class="modal-value" style="color: #ef4444; font-weight: bold;">{{ selectedHistory.total_prints }} lần</span></div>
            <div class="modal-detail-row"><span class="modal-label">Thời gian in:</span><span class="modal-value">{{ selectedHistory.time }}</span></div>
          </div>
          <div class="modal-btn-row">
            <button class="modal-delete-btn" @click="deleteHistory">Xóa</button>
            <button class="modal-close-btn" @click="closeModal">Đóng</button>
            <button class="modal-reprint-btn" @click="reprint">In lại</button>
          </div>
        </div>
      </template>
    </Modal>
  </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import axios from 'axios';

export default {
  components: {
    AuthenticatedLayout,
    Head,
    Modal
  },
  setup() {
    const { props } = usePage();
    const printHistories = ref(props.printHistories || { data: [], current_page: 1, last_page: 1 });
    const showModal = ref(false);
    const selectedHistory = ref(null);

    const openModal = (history) => {
      selectedHistory.value = history;
      showModal.value = true;
    };
    const closeModal = () => {
      showModal.value = false;
      selectedHistory.value = null;
    };
    const nextPage = () => {
      if (printHistories.value.current_page < printHistories.value.last_page) {
        window.location.href = `/print-history?page=${printHistories.value.current_page + 1}`;
      }
    };
    const prevPage = () => {
      if (printHistories.value.current_page > 1) {
        window.location.href = `/print-history?page=${printHistories.value.current_page - 1}`;
      }
    };
    const deleteHistory = async () => {
      if (!selectedHistory.value) return;
      if (!confirm('Bạn có chắc chắn muốn xóa lịch sử in này?')) return;
      try {
        await axios.delete(`/print-history/${selectedHistory.value.id}`);
        closeModal();
        // Reload lại trang để cập nhật danh sách
        window.location.reload();
      } catch (error) {
        alert('Xóa không thành công!');
      }
    };

    const reprint = async () => {
      if (!selectedHistory.value) return;
      const history = selectedHistory.value;
      
      // Khôi phục địa chỉ nhận
      let addressStr = '';
      if (history.address) {
        addressStr = `<p class="print-large"><strong>ĐỊA CHỈ:</strong> <span class="print-italic">${history.address.toUpperCase()}</span></p>`;
      } else {
        try {
          const response = await axios.get('/contacts');
          const contacts = response.data;
          const matchedContact = contacts.find(c => c.phone === history.phone);
          if (matchedContact && matchedContact.address) {
            addressStr = `<p class="print-large"><strong>ĐỊA CHỈ:</strong> <span class="print-italic">${matchedContact.address.toUpperCase()}</span></p>`;
          }
        } catch(e) {
          console.error("Không lấy được danh bạ", e);
        }
      }

      // Khôi phục thông tin người gửi
      let senderContactStr = '';
      let senderAddressStr = '';
      if (history.sender_phone || history.sender_address) {
        senderContactStr = history.sender_phone ? `<p class="print-bold">${history.sender_phone}</p>` : '';
        senderAddressStr = history.sender_address ? `<p class="print-bold">Địa chỉ: ${history.sender_address}</p>` : '';
      } else {
        try {
          const response = await axios.get('/senders');
          const senders = response.data;
          const matchedSender = senders.find(s => s.name === history.sender_name);
          if (matchedSender) {
            senderContactStr = `<p class="print-bold">${matchedSender.contact || ''}</p>`;
            senderAddressStr = `<p class="print-bold">Địa chỉ: ${matchedSender.address || ''}</p>`;
          }
        } catch(e) {
           console.error("Không lấy được người gửi", e);
        }
      }

      const senderInfo = `
        <p class="print-bold">${history.sender_name || ''}</p>
        ${senderContactStr}
        ${senderAddressStr}
      `;
      const receiverInfo = `
        ${history.name ? `<p class="print-bold print-large">TÊN: ${history.name.toUpperCase()}</p>` : ''}
        ${history.phone ? `<p class="print-large"><strong>SĐT:</strong> <span class="print-italic">${history.phone}</span></p>` : ''}
        ${addressStr}
        ${history.shipping_unit ? `<p class="print-italic"><strong>ĐVVC:</strong> ${history.shipping_unit}</p>` : ''}
        ${history.cod ? `<p class="print-italic"><strong>COD:</strong> ${history.cod}</p>` : ''}
        ${history.product_name ? `<p class="print-italic"><strong>TÊN HÀNG:</strong> ${history.product_name}</p>` : ''}
        ${history.payer ? `<p class="print-italic"><strong>TIỀN CƯỚC:</strong> ${history.payer}</p>` : ''}
      `;
      const quantity = Number(history.quantity) || 1;
      let allBills = '';
      for (let i = 1; i <= quantity; i++) {
        allBills += `
          <div class="mainPrints">
            <div class="section">${senderInfo}</div>
            <div class="separator"></div>
            <div class="section">${receiverInfo}</div>
            ${quantity > 1 ? `<div class="bill-serial"><span>${i}/${quantity}</span></div>` : ''}
          </div>
        `;
      }
      const content = `
        <html>
          <head>
            <title>In lại phiếu ${history.name}</title>
            <style>
              body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 0; }
              .mainPrints { width: 105mm; height: 74mm; background: white; border: solid 1px #000; padding: 3mm; box-sizing: border-box; font-size: 10px; font-weight: 500; position: relative; margin: auto; }
              @media print { .mainPrints { page-break-after: always; } .mainPrints:last-of-type { page-break-after: avoid; } @page { size: 105mm 74mm; margin: 0; } }
              .separator { border-top: 1px solid #000; margin: 2mm 0; }
              .print-bold { font-weight: bold; font-size: 15px; margin: 2px; }
              .print-large { font-size: 18px; margin: 4px; }
              .print-small, .print-italic { font-size: 16px; margin: 3px; font-style: italic; }
              .bill-serial { position: absolute; bottom: 8px; right: 12px; z-index: 10; }
              .bill-serial span { display: inline-block; border: 2.5px solid #222; border-radius: 50%; padding: 10px 18px; font-size: 20px; font-weight: bold; color: #222; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
            </style>
          </head>
          <body>
            ${allBills}
            <script>
              window.print();
              window.onafterprint = function() { window.close(); }
            <\\/script>
          </body>
        </html>
      `;
      const printWindow = window.open('', '_blank');
      if(printWindow) {
        printWindow.document.open();
        printWindow.document.write(content);
        printWindow.document.close();
        printWindow.focus();
      }
    };

    return {
      printHistories,
      nextPage,
      prevPage,
      showModal,
      selectedHistory,
      openModal,
      closeModal,
      deleteHistory,
      reprint
    };
  }
}
</script>


<style scoped>
.history-card {
  background: #f9f9f9;
  border: 1px solid #eaeaea;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  transition: transform 0.3s ease;
}

.history-card:hover {
  transform: translateY(-5px);
}

.history-card h4 {
  font-size: 1.25rem; /* Adjusted for better readability */
  color: #333;
  margin-bottom: 15px;
}

.history-card p {
  font-size: 1rem; /* Adjusted for better readability */
  margin: 5px 0;
  display: flex;
  align-items: center;
}

.history-card i {
  margin-right: 10px;
  color: #666;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 20px;
}

.pagination button {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 10px 20px;
  margin: 0 5px;
  border-radius: 5px;
  cursor: pointer;
}

.pagination button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.pagination span {
  margin: 0 10px;
}

.modal-detail-wrapper {
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.18);
  padding: 36px 32px 28px 32px;
  max-width: 600px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  animation: fadeInModal 0.25s;
}
@keyframes fadeInModal {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
.modal-title {
  font-size: 1.7rem;
  font-weight: bold;
  color: #2563eb;
  margin-bottom: 28px;
  text-align: center;
}
.modal-detail-list {
  width: 100%;
  margin-bottom: 18px;
}
.modal-detail-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 14px;
  font-size: 1.08rem;
}
.modal-label {
  font-weight: 600;
  color: #1e293b;
  min-width: 160px;
  text-align: left;
}
.modal-value {
  color: #374151;
  text-align: right;
  word-break: break-word;
  max-width: 220px;
}
.modal-btn-row {
  display: flex;
  justify-content: center;
  gap: 18px;
  margin-top: 18px;
  width: 100%;
}
.modal-delete-btn, .modal-close-btn {
  margin-top: 0;
}
.modal-delete-btn {
  margin-top: 18px;
  margin-right: 12px;
  background: linear-gradient(90deg, #ef4444 0%, #b91c1c 100%);
  color: #fff;
  border: none;
  padding: 12px 38px;
  border-radius: 8px;
  font-size: 1.08rem;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(239,68,68,0.08);
  transition: background 0.2s, transform 0.1s;
}
.modal-delete-btn:hover {
  background: linear-gradient(90deg, #b91c1c 0%, #ef4444 100%);
  transform: translateY(-2px) scale(1.04);
}
.modal-close-btn {
  margin-top: 18px;
  background: linear-gradient(90deg, #2563eb 0%, #1e40af 100%);
  color: #fff;
  border: none;
  padding: 12px 38px;
  border-radius: 8px;
  font-size: 1.08rem;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(37,99,235,0.08);
  transition: background 0.2s, transform 0.1s;
}
.modal-close-btn:hover {
  background: linear-gradient(90deg, #1e40af 0%, #2563eb 100%);
  transform: translateY(-2px) scale(1.04);
}
.modal-reprint-btn {
  margin-top: 18px;
  background: linear-gradient(90deg, #10b981 0%, #047857 100%);
  color: #fff;
  border: none;
  padding: 12px 38px;
  border-radius: 8px;
  font-size: 1.08rem;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(16,185,129,0.08);
  transition: background 0.2s, transform 0.1s;
}
.modal-reprint-btn:hover {
  background: linear-gradient(90deg, #047857 0%, #10b981 100%);
  transform: translateY(-2px) scale(1.04);
}
@media (max-width: 600px) {
  .modal-detail-wrapper {
    padding: 18px 6px 18px 6px;
    max-width: 98vw;
  }
  .modal-label {
    min-width: 90px;
    font-size: 0.98rem;
  }
  .modal-value {
    font-size: 0.98rem;
    max-width: 120px;
  }
}
</style>