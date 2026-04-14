<template>
    <div v-if="isVisible" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Thông tin người gửi</h3>
          <span class="close" @click="close">×</span>
        </div>
        <form @submit.prevent="save">
          <div class="form-group">
            <label for="senderName">Tên cửa hàng:</label>
            <input type="text" v-model="senderInfo.name" id="senderName" placeholder="Nhập tên cửa hàng" required />
          </div>
          <div class="form-group">
            <label for="senderContact">Liên hệ:</label>
            <input type="text" v-model="senderInfo.contact" id="senderContact" placeholder="Nhập số liên hệ" required />
          </div>
          <div class="form-group">
            <label for="senderAddress">Địa chỉ:</label>
            <input type="text" v-model="senderInfo.address" id="senderAddress" placeholder="Nhập địa chỉ" required />
          </div>
          <div class="modal-footer">
            <button type="button" class="cancel-button" @click="close">Hủy</button>
            <button type="submit" class="save-button">Lưu</button>
          </div>
        </form>
        <div class="sender-list">
          <h4>Danh sách người gửi đã lưu</h4>
          <ul>
            <li v-for="sender in senders" :key="sender.id">
              <span @click="selectSender(sender)" style="cursor:pointer;">
                {{ sender.name }} - {{ sender.contact }} - {{ sender.address }}
              </span>
              <!-- <button class="edit-btn" @click.stop="selectSender(sender)">Sửa</button> -->
              <button class="delete-btn" @click.stop="$emit('deleteSender', sender)">Xóa</button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </template>
  
  <script>
    export default {
    props: {
        isVisible: { type: Boolean, required: true },
        senderInfo: { type: Object, required: true },
        senders: { type: Array, required: true },
        saveSender: { type: Function, required: true },
        selectSender: { type: Function, required: true },
        close: { type: Function, required: true },
    },
    methods: {
        save() {
            if (!this.senderInfo.name || !this.senderInfo.contact || !this.senderInfo.address) {
                alert("Vui lòng điền đầy đủ thông tin.");
                return;
            }
            this.saveSender(this.senderInfo);
            this.close(); // Đóng modal nếu cần
        },
    },
};
  </script>
  
  <style scoped>
  .modal {
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.6);
    animation: fadeIn 0.3s ease;
  }
  
  .modal-content {
    background-color: #fff;
    border-radius: 8px;
    padding: 20px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    animation: slideIn 0.3s ease;
  }
  
  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }
  
  .close {
    color: #333;
    font-size: 24px;
    cursor: pointer;
  }
  
  .close:hover {
    color: #e74c3c;
  }
  
  .form-group {
    margin-bottom: 15px;
  }
  
  .form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
  }
  
  .form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
  }
  
  .modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
  }
  
  .cancel-button,
  .save-button {
    padding: 10px 20px;
    font-size: 14px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .cancel-button {
    background-color: #e74c3c;
    color: #fff;
  }
  
  .cancel-button:hover {
    background-color: #c0392b;
  }
  
  .save-button {
    background-color: #4CAF50;
    color: #fff;
  }
  
  .save-button:hover {
    background-color: #45a049;
  }
  
  .sender-list {
    margin-top: 20px;
  }
  
  .sender-list ul {
    list-style: none;
    padding: 0;
  }
  
  .sender-list li {
    cursor: pointer;
    padding: 8px;
    border: 1px solid #ddd;
    margin-top: 5px;
  }
  
  .sender-list li:hover {
    background-color: #f0f0f0;
  }
  
  .edit-btn, .delete-btn {
    margin-left: 8px;
    padding: 4px 10px;
    border-radius: 4px;
    border: none;
    font-size: 13px;
    cursor: pointer;
  }
  .edit-btn { background: #2563eb; color: #fff; }
  .delete-btn { background: #e74c3c; color: #fff; }
  .edit-btn:hover { background: #1e40af; }
  .delete-btn:hover { background: #c0392b; }
  
  @keyframes fadeIn {
    from {
      background-color: rgba(0, 0, 0, 0);
    }
    to {
      background-color: rgba(0, 0, 0, 0.6);
    }
  }
  
  @keyframes slideIn {
    from {
      transform: translateY(-20px);
      opacity: 0;
    }
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }
  </style>