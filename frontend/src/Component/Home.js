import React, {
  useEffect,
  useState,
  useContext
} from "react";

import axios from "axios";

import { Link } from "react-router-dom";

import { CartContext } from "../Context/CartContext";

const Home = () => {

  const [products, setProducts] = useState([]);

  const { addToCart } =
    useContext(CartContext);

  // fetch product
  useEffect(() => {

    const fetchProduct = async () => {

      try {

        const res = await axios.get(
          "http://shoppe.test/api/product"
        );

        setProducts(res.data.data);

      } catch (err) {

        console.log("API ERROR:", err);

      }

    };

    fetchProduct();

  }, []);

  // build image
  const buildImageUrl = (item) => {

    let imgArray = [];

    try {

      imgArray = JSON.parse(item.image);

    } catch {

      imgArray = [];

    }

    const firstImage = imgArray[0];

    return (
      "http://shoppe.test/upload/product/" +
      firstImage
    );

  };

  return (
    <div>

      <section>

        <div className="container">

          <div className="row">

            <div className="col-sm-9 padding-right">

              {/* FEATURES ITEMS */}
              <div className="features_items">

                <h2 className="title text-center">
                  Features Items
                </h2>

                {products.map((item) => {

                  const imageUrl =
                    buildImageUrl(item);

                  return (

                    <div
                      className="col-sm-4"
                      key={item.id}
                    >

                      <div className="product-image-wrapper">

                        <div className="single-products">

                          <div className="productinfo text-center">

                            <img
                              src={imageUrl}
                              alt=""
                              style={{
                                width: "100%",
                                height: 250,
                                objectFit: "cover",
                              }}
                            />

                            <h2>${item.price}</h2>

                            <p>{item.name}</p>

                            <button
                              className="btn btn-default add-to-cart"
                              onClick={() =>
                                addToCart(item.id)
                              }
                            >
                              <i className="fa fa-shopping-cart"></i>

                              Add to cart

                            </button>

                          </div>

                          {/* overlay */}
                          <div className="product-overlay">

                            <div className="overlay-content">

                              <h2>${item.price}</h2>

                              <p>{item.name}</p>

                              <button
                                className="btn btn-default add-to-cart"
                                onClick={() =>
                                  addToCart(item.id)
                                }
                              >
                                <i className="fa fa-shopping-cart"></i>

                                Add to cart

                              </button>

                            </div>

                          </div>

                        </div>

                        <div className="choose">

                          <ul className="nav nav-pills nav-justified">

                            <li>

                              <Link to={`/product/${item.id}`}>

                                <i className="fa fa-plus-square"></i>

                                Detail

                              </Link>

                            </li>

                          </ul>

                        </div>

                      </div>

                    </div>

                  );
                })}

              </div>
              {/* FEATURES ITEMS */}

            </div>

          </div>

        </div>

      </section>

    </div>
  );
};

export default Home;