import React, { useEffect, useState } from "react";
import axios from "axios";
import { Link } from "react-router-dom";
import { useDispatch, useSelector } from "react-redux";
import { addToCart } from "../Redux/cartSlice";
import { toggleWishlist } from "../Redux/wishlistSlice";

const Home = () => {
  const [products, setProducts] = useState([]);
  const [recommendSource, setRecommendSource] = useState([]);

  const dispatch = useDispatch();
  const wishlistIds = useSelector((state) => state.wishlist.items);

  // Lấy danh sách sản phẩm 
  useEffect(() => {
    const fetchProduct = async () => {
      try {
        const res = await axios.get(
          "http://localhost/laravel8/laravel8/public/api/product"
        );
        setProducts(res.data.data);
      } catch (err) {
        console.log("API ERROR:", err);
      }
    };

    fetchProduct();
  }, []);

  // Lấy data cho recommend 
  useEffect(() => {
    const fetchRecommend = async () => {
      try {
        const res = await axios.get(
          "http://localhost/laravel8/laravel8/public/api/product/wishlist"
        );
        setRecommendSource(res.data.data || []);
      } catch (err) {
        console.log("WISHLIST API ERROR:", err);
      }
    };

    fetchRecommend();
  }, []);

  // Lọc 
  const wishlistProducts = recommendSource.filter((p) =>
    wishlistIds.includes(p.id)
  );

  const buildImageUrl = (item) => {
    let imgArray = [];
    try {
      imgArray = JSON.parse(item.image);
    } catch {
      imgArray = [];
    }
    const firstImage = imgArray[0];
    return (
      "http://localhost/laravel8/laravel8/public/upload/product/" +
      item.id_user +
      "/" +
      firstImage
    );
  };

  return (
    <div>
      <section>
        <div className="container">
          <div className="row">

            <div className="col-sm-9 padding-right">

              {/* FEATURES ITEMS  */}
              <div className="features_items">
                <h2 className="title text-center">Features Items</h2>

                {products.map((item) => {
                  const imageUrl = buildImageUrl(item);
                  const isInWishlist = wishlistIds.includes(item.id);

                  return (
                    <div className="col-sm-4" key={item.id}>
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

                            {/* Add to cart */}
                            <button
                              className="btn btn-default add-to-cart"
                              onClick={() => dispatch(addToCart(item.id))}
                            >
                              <i className="fa fa-shopping-cart"></i>
                              Add to cart
                            </button>
                          </div>

                          {/* Overlay */}
                          <div className="product-overlay">
                            <div className="overlay-content">
                              <h2>${item.price}</h2>
                              <p>{item.name}</p>

                              <button
                                className="btn btn-default add-to-cart"
                                onClick={() => dispatch(addToCart(item.id))}
                              >
                                <i className="fa fa-shopping-cart"></i>
                                Add to cart
                              </button>
                            </div>
                          </div>

                        </div>

                        {/* Choose */}
                        <div className="choose">
                          <ul className="nav nav-pills nav-justified">
                            <li>
                              <a
                                href="#"
                                onClick={(e) => {
                                  e.preventDefault();
                                  dispatch(toggleWishlist(item.id));
                                }}
                              >
                                <i className="fa fa-plus-square"></i>
                                {isInWishlist
                                  ? "Remove from wishlist"
                                  : "Add to wishlist"}
                              </a>
                            </li>

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
              {/*   FEATURES ITEMS  */}


              {/*  CATEGORY TAB*/}
              <div className="category-tab">
                <div className="col-sm-12">
                  <ul className="nav nav-tabs">
                    <li className="active">
                      <a href="#tshirt" data-toggle="tab">
                        T-Shirt
                      </a>
                    </li>
                    <li><a href="#blazers" data-toggle="tab">Blazers</a></li>
                    <li><a href="#sunglass" data-toggle="tab">Sunglass</a></li>
                    <li><a href="#kids" data-toggle="tab">Kids</a></li>
                    <li><a href="#poloshirt" data-toggle="tab">Polo shirt</a></li>
                  </ul>
                </div>

                <div className="tab-content">
                  <div className="tab-pane fade active in" id="tshirt">
                    <div className="col-sm-3">
                      <div className="product-image-wrapper">
                        <div className="single-products">
                          <div className="productinfo text-center">
                            <img src="/frontend/images/home/gallery1.jpg" alt="" />
                          </div>
                        </div>
                      </div>
                    </div>

                    <div className="col-sm-3">
                      <div className="product-image-wrapper">
                        <div className="single-products">
                          <div className="productinfo text-center">
                            <img src="/frontend/images/home/gallery2.jpg" alt="" />
                          </div>
                        </div>
                      </div>
                    </div>

                    <div className="col-sm-3">
                      <div className="product-image-wrapper">
                        <div className="single-products">
                          <div className="productinfo text-center">
                            <img src="/frontend/images/home/gallery3.jpg" alt="" />
                          </div>
                        </div>
                      </div>
                    </div>

                    <div className="col-sm-3">
                      <div className="product-image-wrapper">
                        <div className="single-products">
                          <div className="productinfo text-center">
                            <img src="/frontend/images/home/gallery4.jpg" alt="" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
              {/*   CATEGORY TAB*/}


              {/* RECOMMENDED ITEMS  */}
              <div className="recommended_items">
                <h2 className="title text-center">recommended items</h2>

                <div
                  id="recommended-item-carousel"
                  className="carousel slide"
                  data-ride="carousel"
                >
                  <div className="carousel-inner">

                    {wishlistProducts.length > 0 ? (
                      //Nmap 
                      <div className="item active">
                        {wishlistProducts.slice(0, 3).map((prod) => {
                          const imgUrl = buildImageUrl(prod);
                          return (
                            <div className="col-sm-4" key={prod.id}>
                              <div className="product-image-wrapper">
                                <div className="single-products">
                                  <div className="productinfo text-center">
                                    <img src={imgUrl} alt="" />
                                    <h2>${prod.price}</h2>
                                    <p>{prod.name}</p>
                                    <button
                                      className="btn btn-default add-to-cart"
                                      onClick={() => dispatch(addToCart(prod.id))}
                                    >
                                      <i className="fa fa-shopping-cart"></i>
                                      Add to cart
                                    </button>
                                  </div>
                                </div>
                              </div>
                            </div>
                          );
                        })}
                      </div>
                    ) : (
                      <></>
                    )}

                  </div>

                  {/* nút*/}
                  <a
                    className="left recommended-item-control"
                    href="#recommended-item-carousel"
                    data-slide="prev"
                  >
                    <i className="fa fa-angle-left"></i>
                  </a>
                  <a
                    className="right recommended-item-control"
                    href="#recommended-item-carousel"
                    data-slide="next"
                  >
                    <i className="fa fa-angle-right"></i>
                  </a>
                </div>
              </div>
              {/*RECOMMENDED ITEMS  */}


            </div>

          </div>
        </div>
      </section>
    </div>
  );
};

export default Home;
