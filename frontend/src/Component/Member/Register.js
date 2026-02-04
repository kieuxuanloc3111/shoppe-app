import React, { useState } from "react";
import axios from "axios";

const Register = () => {
  const [form, setForm] = useState({
    name: "",
    email: "",
    password: "",
    phone: "",
    address: "",
    avatar: "",  // base64 string
    level: 0,
  });

  const [file, setFile] = useState(null);
  const [errors, setErrors] = useState({});
  const [apiError, setApiError] = useState("");

  const handleInput = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleAvatar = (e) => {
    let fileData = e.target.files[0];
    let reader = new FileReader();

    reader.onload = (event) => {
      setForm({ ...form, avatar: event.target.result }); 
      setFile(fileData);
    };

    reader.readAsDataURL(fileData);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    setApiError("");

    let newErrors = {};
    if (!form.name) newErrors.name = "Chưa nhập name";
    if (!form.email) newErrors.email = "Chưa nhập Email";
    if (!form.password) newErrors.password = "Chưa nhập pass";
    if (!form.phone) newErrors.phone = "Chưa nhập phone";
    if (!form.address) newErrors.address = "Chưa nhập address";

    if (!file) {
      newErrors.avatar = "Chưa chọn avatar";
    } else {
      if (!file.type.includes("image")) newErrors.avatar = "File phải là hình ảnh";
      if (file.size > 1024 * 1024) newErrors.avatar = "Ảnh phải dưới 1MB";
    }

    if (Object.keys(newErrors).length > 0) {
      setErrors(newErrors);
      return;
    }

    setErrors({});

    // JSON payload with base64
    const payload = {
      name: form.name,
      email: form.email,
      password: form.password,
      phone: form.phone,
      address: form.address,
      level: form.level,
      avatar: form.avatar, // base64 string
    };

    try {
      const res = await axios.post(
        "http://localhost/laravel8/laravel8/public/api/register",
        payload,
        { headers: { "Content-Type": "application/json" } }
      );

      console.log("API trả về:", res.data);
      console.log("thông tin ảnh : ",file);

      // Kiểm tra lỗi BE trả về trong JSON
      if (res.data.errors) {
        if (res.data.errors.email) {
          setApiError(res.data.errors.email[0]); 
          return;
        }
      }

      alert("Đăng ký thành công!");

    } catch (error) {
      console.error("Lỗi thực sự:", error);
      setApiError("Không thể kết nối server");
    }
  };

  return (
    <section id="form" style={{ marginTop: "50px" }}>
      <div className="container">
        <div className="row">

          <div className="col-sm-8 col-sm-offset-2">
            <div className="signup-form">

              <h2>New User Signup!</h2>

              {apiError && (
                <p style={{ color: "red", fontWeight: "bold" }}>{apiError}</p>
              )}

              <form onSubmit={handleSubmit}>

                <input
                  type="text"
                  name="name"
                  placeholder="Name"
                  className="form-control"
                  onChange={handleInput}
                />
                <p style={{ color: "red" }}>{errors.name}</p>

                <input
                  type="email"
                  name="email"
                  placeholder="Email Address"
                  className="form-control"
                  onChange={handleInput}
                />
                <p style={{ color: "red" }}>{errors.email}</p>

                <input
                  type="password"
                  name="password"
                  placeholder="Password"
                  className="form-control"
                  onChange={handleInput}
                />
                <p style={{ color: "red" }}>{errors.password}</p>

                <input
                  type="text"
                  name="phone"
                  placeholder="Phone"
                  className="form-control"
                  onChange={handleInput}
                />
                <p style={{ color: "red" }}>{errors.phone}</p>

                <input
                  type="text"
                  name="address"
                  placeholder="Address"
                  className="form-control"
                  onChange={handleInput}
                />
                <p style={{ color: "red" }}>{errors.address}</p>

                <label style={{ marginTop: "10px" }}>Avatar:</label>
                <input
                  type="file"
                  className="form-control"
                  onChange={handleAvatar}
                />
                <p style={{ color: "red" }}>{errors.avatar}</p>

                <button type="submit" className="btn btn-default">
                  Signup
                </button>

              </form>

            </div>
          </div>

        </div>
      </div>
    </section>
  );
};

export default Register;