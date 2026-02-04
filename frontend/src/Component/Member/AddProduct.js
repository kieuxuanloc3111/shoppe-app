import React, { useEffect, useState } from "react";
import axios from "axios";

const AddProduct = () => {
  const [form, setForm] = useState({
    name: "",
    price: "",
    category: "",
    brand: "",
    status: 1,
    sale: "",
    company: "",
    detail: "",
  });

  const [avatar, setAvatar] = useState([]); 
  const [errors, setErrors] = useState({});
  const [categories, setCategories] = useState([]);
  const [brands, setBrands] = useState([]);

 
  useEffect(() => {
    const fetchData = async () => {
      const res = await axios.get(
        "http://localhost/laravel8/laravel8/public/api/category-brand"
      );

      setCategories(res.data.category );
      setBrands(res.data.brand );
    };

    fetchData();
  }, []);

  //  input
  const handleInput = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleStatusChange = (e) => {
    const value = Number(e.target.value);
 
    setForm({  
      ...form,
      status: value,
      sale: value === 1 ? "" : form.sale,
    });
  };

  // upload anh
  const handleFiles = (e) => {
    const files = Array.from(e.target.files);

    let newErrors = {};

    if (avatar.length + 1 > 3) {
      newErrors.images = "Chỉ được upload tối đa 3 ảnh!";
      setErrors(newErrors);
      return;
    }

    files.forEach((file) => {
      if (!file.type.includes("image")) {
        newErrors.images = "File phải là hình ảnh";
      }
      if (file.size > 1024 * 1024) {
        newErrors.images = "Mỗi ảnh phải dưới 1MB";
      }
    });

    if (Object.keys(newErrors).length > 0) {
      setErrors(newErrors);
      return;
    }

    setErrors({});
    setAvatar((prev) => [...prev, ...files]);
  };

  const handleRemoveImage = (index) => {
    setAvatar((prev) => prev.filter((_, i) => i !== index));
  };


  // submit
  const handleSubmit = async (e) => {
    e.preventDefault();

    let newErrors = {};

    if (!form.name) newErrors.name = "Chưa nhập tên";
    if (!form.price) newErrors.price = "Chưa nhập giá";
    if (!form.category) newErrors.category = "Chưa chọn category";
    if (!form.brand) newErrors.brand = "Chưa chọn brand";
    if (avatar.length === 0) newErrors.images = "Phải upload ảnh";

    if (Object.keys(newErrors).length > 0) {
      setErrors(newErrors);
      return;
    }

    const token = localStorage.getItem("token");
    const auth = JSON.parse(localStorage.getItem("auth"));

    const formData = new FormData();
    formData.append("user_id", auth.id);
    formData.append("name", form.name);
    formData.append("price", form.price);
    formData.append("category", form.category);
    formData.append("brand", form.brand);
    formData.append("status", form.status);
    formData.append("sale", form.status === 0 ? form.sale : "");
    formData.append("company", form.company);
    formData.append("detail", form.detail);

    Object.keys(avatar).map((key) => {
      formData.append("file[]", avatar[key]);
    });

    try {
      const res = await axios.post(
        "http://localhost/laravel8/laravel8/public/api/user/product/add",
        formData,
        {
          headers: {
            Authorization: "Bearer " + token,
            "Content-Type": "multipart/form-data",
          },
        }
      );

      console.log("api trả về", res.data);
      alert("Thêm sản phẩm thành công");

    } catch (err) {
      console.log("api err:", err);
      alert("Thêm sản phẩm thất bại");
    }
  };


  return (
    <div className="col-sm-9">
      <div className="blog-post-area">
        <h2 className="title text-center">Add Product</h2>

        <div className="signup-form">
          <form onSubmit={handleSubmit}>

            {/* name */}
            <input
              type="text"
              name="name"
              placeholder="Name"
              value={form.name}
              onChange={handleInput}
            />
            <p style={{ color: "red" }}>{errors.name}</p>

            {/* price */}
            <input
              type="text"
              name="price"
              placeholder="Price"
              value={form.price}
              onChange={handleInput}
            />
            <p style={{ color: "red" }}>{errors.price}</p>

            {/* categỏy */}
            <select
              name="category"
              value={form.category}
              onChange={handleInput}
              className="form-control"
              style={{ marginBottom: 15 }}
            >
              <option value="">Choose category</option>
              {categories.map((item) => (
                <option key={item.id} value={item.id}>
                  {item.category}
                </option>
              ))}
            </select>
            <p style={{ color: "red" }}>{errors.category}</p>

            {/* brand */}
            <select
              name="brand"
              value={form.brand}
              onChange={handleInput}
              className="form-control"
              style={{ marginBottom: 15 }}
            >
              <option value="">Choose brand</option>
              {brands.map((item) => (
                <option key={item.id} value={item.id}>
                  {item.brand}
                </option>
              ))}
            </select>
            <p style={{ color: "red" }}>{errors.brand}</p>

            {/* status */}
            <select
              name="status"
              value={form.status}
              onChange={handleStatusChange}
              className="form-control"
              style={{ marginBottom: 15 }}
            >
              <option value={1}>New</option>
              <option value={0}>Sale</option>
            </select>

            {/* sale */}
            {form.status === 0 && (
              <div style={{ display: "flex", alignItems: "center", marginBottom: 15 }}>
                <input
                  type="text"
                  name="sale"
                  placeholder="Sale"
                  value={form.sale}
                  onChange={handleInput}
                  style={{ flex: 1, marginRight: 5 }}
                />
                <span style={{ fontWeight: "bold" }}>%</span>
              </div>
            )}

            {/* xompany */}
            <input
              type="text"
              name="company"
              placeholder="Company profile"
              value={form.company}
              onChange={handleInput}
              style={{ marginBottom: 15 }}
            />
            <p style={{ color: "red" }}>{errors.company}</p>

            {/* image */}
            <label style={{ marginTop: 10 }}>Images (max 3):</label>
            <input
              type="file"
              name="file[]"
              multiple
              onChange={handleFiles}
              style={{ marginBottom: 10 }}
            />
            <p style={{ color: "red" }}>{errors.images}</p>

            {/* preview */}
            {avatar.length > 0 && (
              <div style={{ display: "flex", gap: 10, marginBottom: 15 }}>
                {avatar.map((img, index) => (
                  <div
                    key={index}
                    style={{ position: "relative", width: 80, height: 80 }}
                  >
                    <img
                      src={URL.createObjectURL(img)}
                      alt=""
                      style={{
                        width: "100%",
                        height: "100%",
                        objectFit: "cover",
                        borderRadius: 5,
                      }}
                    />
                    <button
                      type="button"
                      onClick={() => handleRemoveImage(index)}
                      style={{
                        position: "absolute",
                        top: -8,
                        right: -8,
                        background: "red",
                        color: "white",
                        border: "none",
                        borderRadius: "50%",
                        width: 22,
                        height: 22,
                        cursor: "pointer",
                        fontWeight: "bold",
                      }}
                    >
                      ×
                    </button>
                  </div>
                ))}
              </div>
            )}


            {/* detail */}
            <textarea
              name="detail"
              rows="5"
              placeholder="Detail"
              value={form.detail}
              onChange={handleInput}
            ></textarea>
            <p style={{ color: "red" }}>{errors.detail}</p>

            <button
              type="submit"
              className="btn btn-default"
              style={{ marginTop: 15 }}
            >
              Add Product
            </button>
          </form>
        </div>
      </div>
    </div>
  );
};

export default AddProduct;
