import React, { useState, useEffect, useContext } from "react";
import { Link, useNavigate } from "react-router-dom";
import { CartContext } from "../../Context/CartContext";

const Header = () => {
  const navigate = useNavigate();
  const { cartCount } = useContext(CartContext); 

  const [isLogin, setIsLogin] = useState(false);

  useEffect(() => {
    const checkLogin = () => {
      const token = localStorage.getItem("token");
      setIsLogin(!!token);
    };

    checkLogin();

    window.addEventListener("login", checkLogin);
    window.addEventListener("logout", checkLogin);

    return () => {
      window.removeEventListener("login", checkLogin);
      window.removeEventListener("logout", checkLogin);
    };
  }, []);

  const handleLogout = () => {
    localStorage.clear();
    window.dispatchEvent(new Event("logout"));
    navigate("/login");
  };

  return (
    <>
      <header id="header">
        {/* ===== TOP ===== */}
        <div className="header_top">
          <div className="container">
            <div className="row">
              <div className="col-sm-6">
                <div className="contactinfo">
                  <ul className="nav nav-pills">
                    <li>
                      <a href="#">
                        <i className="fa fa-phone" /> +2 95 01 88 821
                      </a>
                    </li>
                    <li>
                      <a href="#">
                        <i className="fa fa-envelope" /> info@domain.com
                      </a>
                    </li>
                  </ul>
                </div>
              </div>

              <div className="col-sm-6">
                <div className="social-icons pull-right">
                  <ul className="nav navbar-nav">
                    <li>
                      <a href="#"><i className="fa fa-facebook" /></a>
                    </li>
                    <li>
                      <a href="#"><i className="fa fa-twitter" /></a>
                    </li>
                    <li>
                      <a href="#"><i className="fa fa-linkedin" /></a>
                    </li>
                    <li>
                      <a href="#"><i className="fa fa-dribbble" /></a>
                    </li>
                    <li>
                      <a href="#"><i className="fa fa-google-plus" /></a>
                    </li>
                  </ul>
                </div>
              </div>

            </div>
          </div>
        </div>

        {/* ===== MIDDLE ===== */}
        <div className="header-middle">
          <div className="container">
            <div className="row">

              <div className="col-md-4 clearfix">
                <div className="logo pull-left">
                  <Link to="/home">
                    <img src="/frontend/images/home/logo.png" alt="logo" />
                  </Link>
                </div>
              </div>

              <div className="col-md-8 clearfix">
                <div className="shop-menu clearfix pull-right">
                  <ul className="nav navbar-nav">

                    {isLogin && (
                      <>
                        <li>
                          <Link to="/account/update">
                            <i className="fa fa-user"></i> Account
                          </Link>
                        </li>

                        <li>
                          <button
                            onClick={handleLogout}
                            style={{
                              background: "none",
                              border: "none",
                              padding: 0,
                              color: "#337ab7",
                              cursor: "pointer"
                            }}
                          >
                            <i className="fa fa-lock"></i> Logout
                          </button>
                        </li>
                      </>
                    )}

                    {!isLogin && (
                      <li>
                        <Link to="/login">
                          <i className="fa fa-lock"></i> Login
                        </Link>
                      </li>
                    )}

                    <li>
                      <a href="#"><i className="fa fa-star" /> Wishlist</a>
                    </li>

                    <li>
                      <Link to="/checkout">
                        <i className="fa fa-crosshairs"></i> Checkout
                      </Link>
                    </li>

                    {/* CART COUNT */}
                    <li>
                      <Link to="/cart">
                        <i className="fa fa-shopping-cart"></i> Cart{" "}
                        <span
                          className="cart-count"
                          style={{ fontWeight: "bold", color: "#FE980F" }}
                        >
                          ({cartCount})
                        </span>
                      </Link>
                    </li>

                  </ul>
                </div>
              </div>

            </div>
          </div>
        </div>

        {/* ===== BOTTOM ===== */}
        <div className="header-bottom">
          <div className="container">
            <div className="row">

              <div className="col-sm-9">
                <div className="mainmenu pull-left">
                  <ul className="nav navbar-nav collapse navbar-collapse">
                    <li>
                      <Link to="/home">Home</Link>
                    </li>

                    <li className="dropdown">
                      <a href="#">
                        Shop<i className="fa fa-angle-down" />
                      </a>
                      <ul className="sub-menu">
                        <li><a href="#">Products</a></li>
                        <li><a href="#">Product Details</a></li>
                        <li><Link to="/checkout">Checkout</Link></li>
                        <li><Link to="/cart">Cart</Link></li>
                        <li><Link to="/login">Login</Link></li>
                      </ul>
                    </li>

                    <li className="dropdown">
                      <a href="#">
                        Blog<i className="fa fa-angle-down" />
                      </a>
                      <ul className="sub-menu">
                        <li><Link to="/blog">Blog List</Link></li>
                        <li><Link to="/blog_detail">Blog Single</Link></li>
                      </ul>
                    </li>

                    <li><a href="#">404</a></li>
                    <li><a href="#">Contact</a></li>
                  </ul>
                </div>
              </div>

              <div className="col-sm-3">
                <div className="search_box pull-right">
                  <input type="text" placeholder="Search" />
                </div>
              </div>

            </div>
          </div>
        </div>

      </header>
    </>
  );
};

export default Header;
// b