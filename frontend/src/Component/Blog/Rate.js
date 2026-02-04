import React, { useEffect, useState } from "react";
import StarRatings from "react-star-ratings";
import axios from "axios";

const Rate = ({ idBlog }) => {
  const [rating, setRating] = useState(0); 
  const [avgRating, setAvgRating] = useState(0); 
  const [userRate, setUserRate] = useState(0); 
  const [totalRate, setTotalRate] = useState(0); 

  let token = localStorage.getItem("token");
  let auth = localStorage.getItem("auth");
  let user = auth ? JSON.parse(auth) : null;

// get rate
  const fetchRate = async () => {
    try {
      const res = await axios.get(
        `http://localhost/laravel8/laravel8/public/api/blog/rate/${idBlog}`
      );

      const list = res.data.data || [];

      if (list.length > 0) {
        // Tính trung bình
        let sum = 0;
        list.forEach((item) => {
          sum += item.rate;

          // Check user đã từng rate chưa
          if (user && item.user_id == user.id) {
            setUserRate(item.rate);
            setRating(item.rate); // hiển thị lại số sao user đã rate
          }
        });

        setAvgRating(sum / list.length);
        setTotalRate(list.length);
      } else {
        setAvgRating(0);
        setTotalRate(0);
      }
    } catch (err) {
      console.log("Lỗi GET rate:", err);
    }
  };

  useEffect(() => {
    fetchRate();
  }, [idBlog]);


  // 2. POST RATE KHI CLICK SAO

  const changeRating = async (newRating) => {
    if (!user || !token) {
      alert("Bạn phải đăng nhập trước khi đánh giá!");
      return;
    }

    setRating(newRating); // update giao diện

    const url = `http://localhost/laravel8/laravel8/public/api/blog/rate/${idBlog}`;

    const config = {
      headers: {
        Authorization: "Bearer " + token,
        Accept: "application/json",
        "Content-Type": "multipart/form-data",
      },
    };

    const formData = new FormData();
    formData.append("user_id", user.id);
    formData.append("blog_id", idBlog);
    formData.append("rate", newRating);

    try {
      const res = await axios.post(url, formData, config);

      console.log( res.data);

      // Sau khi POST → gọi lại GET để cập nhật trung bình
      fetchRate();
    } catch (err) {
      console.log("Lỗi POST rate:", err);
      alert("Đánh giá thất bại!");
    }
  };

  return (
    <div style={{ marginBottom: 30 }}>
      <h3>Rating</h3>


      <p>
        Trung bình: <strong>{avgRating.toFixed(1)}</strong> ⭐ 
        ({totalRate} lượt đánh giá)
      </p>

 
      <StarRatings
        rating={rating}
        starRatedColor="orange"
        changeRating={changeRating}
        numberOfStars={5}
        name="rating"
        starDimension="30px"
        starSpacing="3px"
      />
    </div>
  );
};

export default Rate;
