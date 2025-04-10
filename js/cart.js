export const CartApp = {
    data() {
      return {
        cart: [],
      }
    },
    created() {
      const stored = localStorage.getItem('cart');
      if (stored) this.cart = JSON.parse(stored);
    },
    computed: {
      cartTotalQty() {
        return this.cart.reduce((sum, item) => sum + item.quantity, 0);
      },
      cartTotal() {
        return this.cart.reduce((sum, item) => sum + item.quantity * item.Price, 0);
      }
    },
    methods: {
      removeFromCart(index) {
        this.cart.splice(index, 1);
        localStorage.setItem('cart', JSON.stringify(this.cart));
      },
      checkout() {
        const member = JSON.parse(localStorage.getItem('member') || 'null');
        if (!member) {
          Swal.fire({ icon: 'info', title: '請先登入' });
          return;
        }
        axios.post('api/order_api.php', {
          cart: this.cart,
          member_id: member.id,
          member_name: member.name,
          member_username: member.username
        }).then(() => {
          Swal.fire({ icon: 'success', title: '訂單已送出' });
          this.cart = [];
          localStorage.removeItem('cart');
        });
      }
    }
  }
  