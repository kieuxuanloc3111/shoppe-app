import React, {
  useEffect,
  useState,
  useContext
} from "react";

import axios from "axios";

import { CartContext } from "../Context/CartContext";

const Cart = () => {

  const [cartItems, setCartItems] = useState([]);

  const [subTotal, setSubTotal] = useState(0);

  const ecoTax = 2;

  const shippingCost = 0;

  // context
  const {
    cart,
    increaseCart,
    decreaseCart,
    removeCart
  } = useContext(CartContext);

  // tính tổng
  const updateTotal = (items) => {

    let sum = 0;

    items.forEach((p) => {
      sum += p.price * p.qty;
    });

    setSubTotal(sum);

  };

  // load cart từ api
  useEffect(() => {

    const fetchCart = async () => {

      if (!cart || Object.keys(cart).length === 0) {

        setCartItems([]);
        setSubTotal(0);

        return;
      }

      try {

        const res = await axios.post(
          "http://shoppe.test/api/product/cart",
          { cart },
          {
            headers: {
              "Content-Type": "application/json"
            }
          }
        );

        let items = res.data.data || [];

        // gán qty từ localStorage/context
        items = items.map((item) => ({
          ...item,
          qty: cart[item.id] || 1
        }));

        setCartItems(items);

        updateTotal(items);

      } catch (err) {

        console.log("CART API ERROR:", err);

      }

    };

    fetchCart();

  }, [cart]);

  const increaseQty = (id) => {
    increaseCart(id);
  };

  const decreaseQty = (id) => {
    decreaseCart(id);
  };

  const removeItem = (id) => {
    removeCart(id);
  };
  // lấy ảnh đầu tiên
  const getImg = (item) => {

    let arr = [];

    try {

      arr = JSON.parse(item.image);

    } catch {

      arr = [];

    }

    return (
      "http://shoppe.test/upload/product/" +
      arr[0]
    );

  };

  return (
    <div>

      <section id="cart_items">

        <div className="container">

          <div className="breadcrumbs">

            <ol className="breadcrumb">

              <li>
                <a href="#">Home</a>
              </li>

              <li className="active">
                Shopping Cart
              </li>

            </ol>

          </div>

          <div className="table-responsive cart_info">

            <table className="table table-condensed">

              <thead>

                <tr className="cart_menu">

                  <td className="image">
                    Item
                  </td>

                  <td className="description"></td>

                  <td className="price">
                    Price
                  </td>

                  <td className="quantity">
                    Quantity
                  </td>

                  <td className="total">
                    Total
                  </td>

                  <td></td>

                </tr>

              </thead>

              <tbody>

                {cartItems.map((item) => (

                  <tr key={item.id}>

                    <td className="cart_product">

                      <a href="#">

                        <img
                          src={getImg(item)}
                          alt=""
                          style={{
                            width: 80,
                            height: 80,
                            objectFit: "cover"
                          }}
                        />

                      </a>

                    </td>

                    <td className="cart_description">

                      <h4>
                        <a href="#">
                          {item.name}
                        </a>
                      </h4>

                      <p>
                        Product ID: {item.id}
                      </p>

                    </td>

                    <td className="cart_price">

                      <p>${item.price}</p>

                    </td>

                    <td className="cart_quantity">

                      <div className="cart_quantity_button">

                        <a
                          className="cart_quantity_up"
                          onClick={() =>
                            increaseQty(item.id)
                          }
                          style={{
                            cursor: "pointer"
                          }}
                        >
                          +
                        </a>

                        <input
                          className="cart_quantity_input"
                          type="text"
                          value={item.qty}
                          readOnly
                        />

                        <a
                          className="cart_quantity_down"
                          onClick={() =>
                            decreaseQty(item.id)
                          }
                          style={{
                            cursor: "pointer"
                          }}
                        >
                          -
                        </a>

                      </div>

                    </td>

                    <td className="cart_total">

                      <p className="cart_total_price">

                        $
                        {item.price * item.qty}

                      </p>

                    </td>

                    <td className="cart_delete">

                      <a
                        className="cart_quantity_delete"
                        onClick={() =>
                          removeItem(item.id)
                        }
                        style={{
                          cursor: "pointer"
                        }}
                      >
                        <i className="fa fa-times"></i>
                      </a>

                    </td>

                  </tr>

                ))}

              </tbody>

            </table>

          </div>

        </div>

      </section>

      {/* total */}
      <section id="do_action">

        <div className="container">

          <div className="row">

            <div className="col-sm-6 col-sm-offset-6">

              <div className="total_area">

                <ul>

                  <li>
                    Cart Sub Total
                    <span>${subTotal}</span>
                  </li>

                  <li>
                    Eco Tax
                    <span>
                      ${cartItems.length * ecoTax}
                    </span>
                  </li>

                  <li>
                    Shipping Cost
                    <span>Free</span>
                  </li>

                  <li>
                    Total
                    <span>
                      $
                      {subTotal +
                        cartItems.length *
                          ecoTax}
                    </span>
                  </li>

                </ul>

              </div>

            </div>

          </div>

        </div>

      </section>

    </div>
  );
};

export default Cart;