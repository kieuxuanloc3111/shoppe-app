import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import axios from "axios";

const ProductDetail = () => {
  const { id } = useParams(); // lấy id sản phẩm

  const [product, setProduct] = useState(null);
  const [categoryName, setCategoryName] = useState("");
  const [brandName, setBrandName] = useState("");
  const [images, setImages] = useState([]);
  const [mainImage, setMainImage] = useState("");

  // Modal Zoom
  const [showZoom, setShowZoom] = useState(false);
  const getImgUrl = (filename) => {
    return `http://localhost:8080/upload/product/${filename}`;
  };

  useEffect(() => {
    const fetchData = async () => {
      try {
        // API lấy chi tiết sản phẩm
        const res = await axios.get(
          `http://localhost:8080/api/product/detail/${id}`
          // `http://shoppe.test/api/product/detail/${id}`
          // http://localhost/laravel8/laravel8/public/api/product/detail/${id}
        );

        const data = res.data.data;
        setProduct(data);

        let imgArr = [];

        if (typeof data.image === "string") {
          try {
            let parsed = JSON.parse(data.image);

            // 🔥 nếu vẫn là string → parse lần nữa
            if (typeof parsed === "string") {
              parsed = JSON.parse(parsed);
            }

            if (Array.isArray(parsed)) {
              imgArr = parsed;
            } else {
              imgArr = [parsed];
            }
          } catch (e) {
            console.log("PARSE ERROR:", e);
            imgArr = [];
          }
        } else if (Array.isArray(data.image)) {
          imgArr = data.image;
        }

        setImages(imgArr);

        setImages(imgArr);
        console.log("IMAGE RAW:", data.image);
        console.log("IMAGE AFTER:", imgArr);
        
        if (imgArr.length > 0) {
          setMainImage(
            getImgUrl(imgArr[0])
            // `http://shoppe.test/upload/product/${imgArr[0]}`
            // http://localhost/laravel8/laravel8/public/upload/product/${data.id_user}/${imgArr[0]}
          );
        }

        // API category + brand
        const cateBrand = await axios.get(
          "http://localhost:8080/api/category-brand"
          // "http://shoppe.test/api/category-brand"
          // http://localhost/laravel8/laravel8/public/api/category-brand
        );
        console.log(getImgUrl(imgArr[0]));
        console.log("FIRST:", images[0]);
        const categories = cateBrand.data.category;
        const brands = cateBrand.data.brand;

        // tìm category
        const cate = categories.find(
          (item) => Number(item.id) === Number(data.category_id)
        );

        // tìm brand
        const brand = brands.find(
          (item) => Number(item.id) === Number(data.brand_id)
        );

        setCategoryName(cate ? cate.category : "");
        setBrandName(brand ? brand.brand : "");
      } catch (err) {
        console.log("ERROR:", err);
      }
    };

    fetchData();
  }, [id]);

  if (!product) return <p>Loading...</p>;

  // Build image URL for thumbnails
  // const getImgUrl = (filename) => {
  //   return `http://localhost:8080/uploads/product/${filename}`;
  //   // return `http://shoppe.test/upload/product/${filename}`;
  //   // http://localhost/laravel8/laravel8/public/upload/product/${product.id_user}/${filename}
  // };

  return (
    <div className="col-sm-9 padding-right">
      <div className="product-details">
        <div className="col-sm-5">
          <div className="view-product">
            <img src={mainImage} alt="" style={{ width: "100%" }} />

            {/* NÚT ZOOM */}
            <button
              onClick={() => setShowZoom(true)}
              style={{
                marginTop: 10,
                padding: "6px 12px",
                background: "#FE980F",
                color: "#fff",
                border: "none",
                cursor: "pointer",
              }}
            >
              ZOOM
            </button>
          </div>

          {/* Thumbnail Image Slider */}
          <div
            id="similar-product"
            className="carousel slide"
            data-ride="carousel"
            style={{ marginTop: 20 }}
          >
            <div className="carousel-inner">
              <div className="item active" style={{ display: "flex", gap: 10 }}>
                {Array.isArray(images) &&
                    images.slice(0, 3).map((img, index) => (
                  <img
                    key={index}
                    src={getImgUrl(img)}
                    alt=""
                    style={{
                      width: 80,
                      height: 80,
                      cursor: "pointer",
                      border: "1px solid #ddd",
                    }}
                    onClick={() => setMainImage(getImgUrl(img))}
                  />
                ))}
              </div>
            </div>
          </div>
        </div>

        {/* RIGHT SIDE INFORMATION */}
        <div className="col-sm-7">
          <div className="product-information">
            <h2>{product.name}</h2>
            <p>Product ID: {product.id}</p>

            {/* PRICE */}
            <span>
              <span style={{ fontSize: 24, color: "orange" }}>
                ${product.price}
              </span>
            </span>

            {/* SALE */}
            {product.sale && (
              <p style={{ fontWeight: "bold", color: "red" }}>
                Sale: {product.sale_price}
              </p>
            )}

            <p>
              <b>Category:</b> {categoryName}
            </p>

            <p>
              <b>Brand:</b> {brandName}
            </p>

            <p>
              <b>Detail:</b> {product.detail}
            </p>

            <p>
              <b>Company:</b> {product.company}
            </p>
          </div>
        </div>
      </div>

      {/* Reviews Section */}
      <div className="category-tab shop-details-tab">
        <div className="col-sm-12">
          <ul className="nav nav-tabs">
            <li className="active">
              <a href="#details" data-toggle="tab">
                Details
              </a>
            </li>
            <li>
              <a href="#companyprofile" data-toggle="tab">
                Company Profile
              </a>
            </li>
            <li>
              <a href="#tag" data-toggle="tab">
                Tag
              </a>
            </li>
            <li>
              <a href="#reviews" data-toggle="tab">
                Reviews (0)
              </a>
            </li>
          </ul>
        </div>

        <div className="tab-content">
          <div className="tab-pane fade active in" id="details">
            <p>{product.detail}</p>
          </div>

          <div className="tab-pane fade" id="companyprofile">
            <p>{product.company_profile}</p>
          </div>

          <div className="tab-pane fade" id="tag">
            <p>No tag</p>
          </div>

          <div className="tab-pane fade" id="reviews">
            <p>No review yet.</p>
          </div>
        </div>
      </div>

      {/* ============================
          MODAL ZOOM FULL MÀN HÌNH
      ============================= */}
      {showZoom && (
        <div
          onClick={() => setShowZoom(false)}
          style={{
            position: "fixed",
            top: 0,
            left: 0,
            width: "100vw",
            height: "100vh",
            background: "rgba(0,0,0,0.8)",
            display: "flex",
            justifyContent: "center",
            alignItems: "center",
            zIndex: 9999,
            cursor: "zoom-out",
          }}
        >
          <img
            src={mainImage}
            alt=""
            style={{
              maxWidth: "90%",
              maxHeight: "90%",
              borderRadius: 4,
            }}
          />
        </div>
      )}
    </div>
  );
};

export default ProductDetail;
