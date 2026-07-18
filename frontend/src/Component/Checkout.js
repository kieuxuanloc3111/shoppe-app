import React, { useEffect, useState } from "react";
import axios from "axios";

const Checkout = () => {
  const [cart, setCart] = useState([]);
  const [form, setForm] = useState({
    name : "",
    phone : "",
  });
  const token = localStorage.getItem("token");
  const auth =JSON.parse(localStorage.getItem("auth"));
  useEffect(() => {

    const fetchCart = async () => {

      let cartLocal = JSON.parse(localStorage.getItem("cart")) || {};

      // nếu rỗng
      if (Object.keys(cartLocal).length === 0) {
        setCart([]);
        return;
      }

      try {

        // gọi API lấy product theo id
        const res = await axios.post(
          "http://shoppe.test/api/product/cart",
          { cart: cartLocal }
        );

        setCart(res.data.data);
        console.log("CART DATA:", res.data.data);

      } catch (err) {
        console.log(err);
      }
    };

    fetchCart();

  }, []);
  
  const handleInput = (e) => {
    setForm({
      ...form,
      [e.target.name] : e.target.value,
    })
  };
  const getTotal = () => {
    return cart.reduce((sum,item)=> sum + item.price * item.qty, 0);
  };

  const handleCheckout = async (e) => {
    e.preventDefault();
    try {

      const res = await axios.post(
        "http://shoppe.test/api/checkout",
        {
          name: form.name,
          phone: form.phone,
          cart: cart
        },
        {
          headers:{
            Authorization: "Bearer " + token
          }
        }
      );

      alert("Checkout thành công!");

      localStorage.removeItem("cart");
      window.location.href = "/home";

    } catch(err){
      console.log(err);
      alert("Checkout thất bại!");
    }
  };
  return (
    <section id="cart_items">
      <div className="container">

        {/* breadcrumb */}
        <div className="breadcrumbs">
          <ol className="breadcrumb">
            <li><a href="/home">Home</a></li>
            <li className="active">Checkout</li>
          </ol>
        </div>

        <div className="shopper-informations">
          <div className="row">

            {/* form */}
            <div className="col-sm-4">
              <div className="shopper-info">
                <p>Shopper Information</p>

                <form onSubmit={handleCheckout}>

                  <input
                    type="text"
                    name="name"
                    placeholder="Your Name"
                    value={form.name}
                    onChange={handleInput}
                  />

                  <input
                    type="text"
                    name="phone"
                    placeholder="Phone"
                    value={form.phone}
                    onChange={handleInput}
                  />

                  <button className="btn btn-primary">
                    Continue
                  </button>

                </form>
              </div>
            </div>

            {/* cart */}
            <div className="col-sm-8">
              <div className="table-responsive cart_info">

                <table className="table table-condensed">
                  <thead>
                    <tr className="cart_menu">
                      <td className="image">Item</td>
                      <td className="description"></td>
                      <td className="price">Price</td>
                      <td className="quantity">Quantity</td>
                      <td className="total">Total</td>
                      <td></td>
                    </tr>
                  </thead>

                  <tbody>

                    {cart.map((item,index)=>{

                      const rowTotal = item.price * item.qty;

                      return (
                        <tr key={index}>

                          <td className="cart_product">
                            <img
                              src={`http://shoppe.test/upload/product/${item.id_user}/${item.image}`}
                              alt=""
                              style={{width:85}}
                            />
                          </td>

                          <td className="cart_description">
                            <h4>{item.name}</h4>
                          </td>

                          <td className="cart_price">
                            <p>${item.price}</p>
                          </td>

                          <td className="cart_quantity">
                            <p>{item.qty}</p>
                          </td>

                          <td className="cart_total">
                            <p className="cart_total_price">${rowTotal}</p>
                          </td>

                        </tr>
                      );
                    })}

                    <tr>
                      <td colSpan="4"></td>
                      <td colSpan="2">

                        <table className="table table-condensed total-result">
                          <tbody>
                            <tr>
                              <td>Cart Sub Total</td>
                              <td>${getTotal()}</td>
                            </tr>
                            <tr>
                              <td>Total</td>
                              <td><span>${getTotal()}</span></td>
                            </tr>
                          </tbody>
                        </table>

                      </td>
                    </tr>

                  </tbody>
                </table>

              </div>
            </div>

          </div>
        </div>

      </div>
    </section>
  );
};
export default Checkout;