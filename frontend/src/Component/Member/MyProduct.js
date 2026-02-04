import React, { useEffect, useState } from "react";
import axios from "axios";

const MyProduct = () => {
  const [products, setProducts] = useState([]);

  useEffect(() => {
    const fetchProduct = async () => {
      try {
        const token = localStorage.getItem("token");

        const res = await axios.get(
          "http://localhost/laravel8/laravel8/public/api/user/my-product",
          {
            headers: { Authorization: "Bearer " + token }
          }
        );

        console.log("API RETURN:", res.data);
        if (res.data?.data && typeof res.data.data === "object") {
          const arr = Object.values(res.data.data); // Chuyển object → array
          setProducts(arr);
        } else {
          setProducts([]);
        }

      } catch (error) {
        console.log("api error:", error);
      }
    };

    fetchProduct();
  }, []);


  const handleDelete = async (idProduct) => {
    if (!window.confirm("Bạn có muốn xóa sản phẩm này?")) return;

    const token = localStorage.getItem("token");

    try {
      const res = await axios.get(
        `http://localhost/laravel8/laravel8/public/api/user/product/delete/${idProduct}`,
        {
          headers: {
            Authorization: "Bearer " + token,
          },
        }
      );

      console.log("delete return:", res.data);

      // filter bỏ sản phẩm vừa delete
      setProducts((prev) => prev.filter((item) => item.id !== idProduct));

      alert("Xóa thành công!");
    } catch (error) {
      console.log("lỗi khi xóa:", error);
      alert("xóa thất bại!");
    }
  };


  return (
    <div className="col-sm-9">
      <div className="table-responsive cart_info">
        <table className="table table-condensed">
          <thead>
            <tr className="cart_menu">
              <td className="image">image</td>
              <td className="description">name</td>
              <td className="price">price</td>
              <td className="total">action</td>
            </tr>
          </thead>

          <tbody>
            {products.map((item) => {
              let imgArray = [];
              try {
                imgArray = JSON.parse(item.image);
              } catch {
                imgArray = [];
              }

              const firstImage = imgArray[0];
              const imageUrl =
                "http://localhost/laravel8/laravel8/public/upload/product/" +
                item.id_user +
                "/" +
                firstImage;

              return (
                <tr key={item.id}>
                  <td className="cart_product">
                    <a href="#">
                      <img
                        src={imageUrl}
                        alt=""
                        style={{ width: 80, height: 80, objectFit: "cover" }}
                      />
                    </a>
                  </td>

                  <td className="cart_description">
                    <h4>
                      <a href="#">{item.name}</a>
                    </h4>
                  </td>

                  <td className="cart_price">
                    <p>${item.price}</p>
                  </td>

                  <td className="cart_total">
                    <a
                      style={{ marginRight: 10, cursor: "pointer" }}
                      href={`/account/product/edit/${item.id}`}
                    >
                      edit
                    </a>

                    <a
                      style={{ cursor: "pointer", color: "red" }}
                      onClick={() => handleDelete(item.id)}
                    >
                      delete
                    </a>
                  </td>
                </tr>
              );
            })}
          </tbody>
        </table>

        {products.length === 0 && (
          <p style={{ padding: 20, color: "gray" }}>
            Bạn chưa có sản phẩm nào.
          </p>
        )}
      </div>
    </div>
  );
};

export default MyProduct;
