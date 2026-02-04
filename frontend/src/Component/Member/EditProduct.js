import React, { useEffect, useState, useRef } from "react";
import axios from "axios";
import { useParams, useNavigate } from "react-router-dom";

const EditProduct = () => {
  const { id } = useParams();
  const token = localStorage.getItem("token");
  const auth = JSON.parse(localStorage.getItem("auth"));

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

  const [existingImages, setExistingImages] = useState([]);
  const [toDelete, setToDelete] = useState([]);
  const [avatar, setAvatar] = useState([]);
  const [newPreviews, setNewPreviews] = useState([]);
  const [errors, setErrors] = useState({});
  const [categories, setCategories] = useState([]);
  const [brands, setBrands] = useState([]);




  useEffect(() => {
    axios
      .get("http://localhost/laravel8/laravel8/public/api/category-brand")
      .then((res) => {
        setCategories(res.data.category);
        setBrands(res.data.brand );
      })
  }, []);

  useEffect(() => {
    axios
      .get(`http://localhost/laravel8/laravel8/public/api/user/product/${id}`, {
        headers: { Authorization: "Bearer " + token },
      })
      .then((res) => {
        const data = res.data.data;

        setForm({
          name: data.name,
          price: data.price,
          category: data.id_category,
          brand: data.id_brand,
          status: data.status,
          sale: data.sale,
          company: data.company_profile,
          detail: data.detail,
        });

        let imgs = data.image;
        setExistingImages(imgs);
      })
      .catch((err) => {
        console.log("lỗi khi load : ", err);
      });
  }, [id, token]);


  useEffect(() => {
    const urls = avatar.map((file) => URL.createObjectURL(file));
    setNewPreviews(urls);

    return () => {
      urls.forEach((u) => URL.revokeObjectURL(u));
    };
  }, [avatar]);


  const handleInput = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleStatusChange = (e) => {
    const value = Number(e.target.value);
    setForm((prev) => ({
      ...prev,
      status: value,
      sale: value === 1 ? "" : prev.sale,
    }));
  };

  const toggleDelete = (filename) => {
    setToDelete((prev) => {
      if (prev.includes(filename)) {
        return prev.filter((f) => f !== filename);
      } else {
        return [...prev, filename];
      }
    });
  };

  const handleFiles = (e) => {
    const files = Array.from(e.target.files);

    const keptExisting = existingImages.length - toDelete.length;
    const totalAfter = keptExisting + avatar.length + 1;

    if (totalAfter > 3) {
      setErrors({ images: 'không được vượt quá 3 ảnh' });

      return;
    }

    setAvatar((prev) => [...prev, ...files]);
    setErrors({});
  };

  const handleRemoveNewFile = (index) => {
    setAvatar((prev) => prev.filter((_, i) => i !== index));
  };

  const getExistingImageUrl = (filename) => {
    const userId = auth?.id || "0";
    return `http://localhost/laravel8/laravel8/public/upload/product/${userId}/${filename}`;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    let newErrors = {};

    if (!form.name) newErrors.name = "Chưa nhập tên";
    if (!form.price) newErrors.price = "Chưa nhập giá";
    if (!form.category) newErrors.category = "Chưa chọn category";
    if (!form.brand) newErrors.brand = "Chưa chọn brand";

    const keptExisting = existingImages.length - toDelete.length;
    const finalCount = keptExisting + avatar.length;

    if (finalCount === 0) {
      newErrors.images = "Phải có ít nhất 1 ảnh ";
    } else if (finalCount > 3) {
      newErrors.images = "không được vượt quá 3 ảnh";
    }

    if (Object.keys(newErrors).length > 0) {
      setErrors(newErrors);
      return;
    }

    const formData = new FormData();
    formData.append("id", id);
    formData.append("user_id", auth?.id);
    formData.append("name", form.name);
    formData.append("price", form.price);
    formData.append("category", form.category);
    formData.append("brand", form.brand);
    formData.append("status", form.status);
    formData.append("sale", form.status === 0 ? form.sale : "");
    formData.append("company", form.company);
    formData.append("detail", form.detail);

    avatar.forEach((file) => {
      formData.append("file[]", file);
    });

    toDelete.forEach((filename) => {
      formData.append("avatarCheckBox[]", filename);
    });

    try {
      const res = await axios.post(
        `http://localhost/laravel8/laravel8/public/api/user/product/update/${id}`,
        formData,
        {
          headers: {
            Authorization: "Bearer " + token,
            "Content-Type": "multipart/form-data",
          },
        }
      );

      console.log("Update Response:", res.data);
      alert("Cập nhật thành công!");
    } catch (err) {
      console.log("Lỗi API:", err);
      alert("Cập nhật thất bại!");
    }
  };

  return (
    <div className="col-sm-9">
      <div className="blog-post-area">
        <h2 className="title text-center">Edit Product</h2>

        <div className="signup-form">
          <form onSubmit={handleSubmit}>

            <input
              type="text"
              name="name"
              placeholder="Name"
              value={form.name}
              onChange={handleInput}
            />
            <p style={{ color: "red" }}>{errors.name}</p>

            <input
              type="text"
              name="price"
              placeholder="Price"
              value={form.price}
              onChange={handleInput}
            />
            <p style={{ color: "red" }}>{errors.price}</p>

            <select
              name="category"
              value={form.category}
              onChange={handleInput}
              className="form-control"
              style={{ marginBottom: 15 }}
            >
              <option value="">Choose category</option>
              {categories.map((item) => (
                <option key={item.id} value={item.id}>{item.category}</option>
              ))}
            </select>
            <p style={{ color: "red" }}>{errors.category}</p>

            <select
              name="brand"
              value={form.brand}
              onChange={handleInput}
              className="form-control"
              style={{ marginBottom: 15 }}
            >
              <option value="">Choose brand</option>
              {brands.map((item) => (
                <option key={item.id} value={item.id}>{item.brand}</option>
              ))}
            </select>
            <p style={{ color: "red" }}>{errors.brand}</p>

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

            {form.status === 0 && (
              <div style={{ marginBottom: 15 }}>
                <input
                  type="text"
                  name="sale"
                  placeholder="Sale"
                  value={form.sale}
                  onChange={handleInput}
                />
              </div>
            )}

            <input
              type="text"
              name="company"
              placeholder="Company profile"
              value={form.company}
              onChange={handleInput}
            />

            <br /><br />

            <label>Existing Images (check to delete):</label>
            <div style={{ display: "flex", gap: 12, flexWrap: "wrap", marginBottom: 12 }}>
              {existingImages.length === 0 && <div style={{ color: "gray" }}>No existing images</div>}
              {existingImages.map((filename) => {
                const checked = toDelete.includes(filename);
                return (
                  <div key={filename} style={{ textAlign: "center" }}>
                    <img
                      src={getExistingImageUrl(filename)}
                      alt={filename}
                      style={{
                        width: 100,
                        height: 100,
                        objectFit: "cover",
                        borderRadius: 6,
                        opacity: checked ? 0.35 : 1,
                        transition: "opacity .15s",
                        display: "block",
                      }}
                    />
                    <label style={{ display: "block", cursor: "pointer" }}>
                      <input
                        type="checkbox"
                        checked={checked}
                        onChange={() => toggleDelete(filename)}
                        style={{ marginRight: 6 }}
                      />
                      Delete
                    </label>
                    <div style={{ maxWidth: 100, wordBreak: "break-word", fontSize: 12 }}>{filename}</div>
                  </div>
                );
              })}
            </div>

            <label>Upload New Images (max total 3):</label>
            
            <input
              type="file"
              multiple
              onChange={handleFiles}
              style={{ marginBottom: 10, display: "block" }}
              accept="image/*"
            />
            <p style={{ color: "red" }}>{errors.images}</p>

            {/* Preview */}
            {newPreviews.length > 0 && (
              <div style={{ display: "flex", gap: 10, marginBottom: 15 }}>
                {newPreviews.map((src, index) => (
                  <div key={index} style={{ position: "relative" }}>
                    <img
                      src={src}
                      alt={`new-${index}`}
                      style={{ width: 80, height: 80, objectFit: "cover", borderRadius: 5 }}
                    />
                    <button
                      type="button"
                      onClick={() => handleRemoveNewFile(index)}
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
                      }}
                    >
                      ×
                    </button>
                  </div>
                ))}
              </div>
            )}

            <textarea
              name="detail"
              rows="5"
              placeholder="Detail"
              value={form.detail}
              onChange={handleInput}
            ></textarea>

            <button type="submit" className="btn btn-default" style={{ marginTop: 15 }}>
              Update Product
            </button>
          </form>
        </div>
      </div>
    </div>
  );
};

export default EditProduct;
