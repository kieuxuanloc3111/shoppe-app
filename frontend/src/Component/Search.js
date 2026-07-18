import React, { useEffect, useState, useContext } from "react";
import axios from "axios";
import { Link, useSearchParams } from "react-router-dom";
import { CartContext } from "../Context/CartContext";

const API = "http://127.0.0.1:8000";

const buildImageUrl = (item) => {
  let arr = [];
  try {
    arr = JSON.parse(item.image);
  } catch {
    arr = [];
  }
  return `${API}/upload/product/${arr[0]}`;
};

const Search = () => {
  const [params] = useSearchParams();
  const keyword = params.get("keyword") || "";

  const { addToCart } = useContext(CartContext);

  const [products, setProducts] = useState([]);
  const [categories, setCategories] = useState([]);
  const [brands, setBrands] = useState([]);
  const [page, setPage] = useState(null); // {current, last} — only advanced search paginates

  // advanced filter form
  const [filter, setFilter] = useState({
    name: "",
    price_range: "",
    category_id: "",
    brand_id: "",
    status: "",
  });
  const [price, setPrice] = useState({ min: "", max: "" });

  // keyword search (from header)
  useEffect(() => {
    axios
      .get(`${API}/api/category-brand`)
      .then((res) => {
        setCategories(res.data.category || []);
        setBrands(res.data.brand || []);
      })
      .catch((e) => console.log(e));
  }, []);

  useEffect(() => {
    if (!keyword) return;
    setPage(null);
    axios
      .get(`${API}/api/search`, { params: { keyword } })
      .then((res) => setProducts(res.data.data || []))
      .catch((e) => console.log(e));
  }, [keyword]);

  // advanced search is paginated (6/page)
  const fetchAdvanced = (pageNum = 1) => {
    axios
      .get(`${API}/api/advanced-search`, { params: { ...filter, page: pageNum } })
      .then((res) => {
        const p = res.data.data; // Laravel paginator
        setProducts(p.data || []);
        setPage({ current: p.current_page, last: p.last_page });
      })
      .catch((e) => console.log(e));
  };

  const runAdvanced = (e) => {
    e.preventDefault();
    fetchAdvanced(1);
  };

  const runPrice = (e) => {
    e.preventDefault();
    setPage(null);
    axios
      .get(`${API}/api/filter-price`, { params: price })
      .then((res) => setProducts(res.data.data || []))
      .catch((e) => console.log(e));
  };

  const onFilter = (e) =>
    setFilter({ ...filter, [e.target.name]: e.target.value });

  return (
    <div className="col-sm-9 padding-right">
      <h2 className="title text-center">
        {keyword ? `Search: "${keyword}"` : "Search products"}
      </h2>

      {/* advanced filter */}
      <form onSubmit={runAdvanced} style={{ marginBottom: 20 }}>
        <input
          name="name"
          placeholder="Name"
          value={filter.name}
          onChange={onFilter}
        />
        <select name="price_range" value={filter.price_range} onChange={onFilter}>
          <option value="">Price range</option>
          <option value="0-100">0 - 100</option>
          <option value="100-500">100 - 500</option>
          <option value="500-1000">500 - 1000</option>
        </select>
        <select name="category_id" value={filter.category_id} onChange={onFilter}>
          <option value="">Category</option>
          {categories.map((c) => (
            <option key={c.id} value={c.id}>
              {c.category}
            </option>
          ))}
        </select>
        <select name="brand_id" value={filter.brand_id} onChange={onFilter}>
          <option value="">Brand</option>
          {brands.map((b) => (
            <option key={b.id} value={b.id}>
              {b.brand}
            </option>
          ))}
        </select>
        <select name="status" value={filter.status} onChange={onFilter}>
          <option value="">Status</option>
          <option value="sale">Sale</option>
          <option value="new">New</option>
        </select>
        <button type="submit" className="btn btn-default">
          Filter
        </button>
      </form>

      {/* price filter (computed sale price) */}
      <form onSubmit={runPrice} style={{ marginBottom: 20 }}>
        <input
          type="number"
          placeholder="Min"
          value={price.min}
          onChange={(e) => setPrice({ ...price, min: e.target.value })}
        />
        <input
          type="number"
          placeholder="Max"
          value={price.max}
          onChange={(e) => setPrice({ ...price, max: e.target.value })}
        />
        <button type="submit" className="btn btn-default">
          Filter price
        </button>
      </form>

      <div className="features_items">
        {products.length === 0 && <p>No products.</p>}
        {products.map((item) => (
          <div className="col-sm-4" key={item.id}>
            <div className="product-image-wrapper">
              <div className="single-products">
                <div className="productinfo text-center">
                  <img
                    src={buildImageUrl(item)}
                    alt=""
                    style={{ width: "100%", height: 250, objectFit: "cover" }}
                  />
                  <h2>${item.price}</h2>
                  <p>{item.name}</p>
                  <button
                    className="btn btn-default add-to-cart"
                    onClick={() => addToCart(item.id)}
                  >
                    <i className="fa fa-shopping-cart"></i> Add to cart
                  </button>
                </div>
              </div>
              <div className="choose">
                <ul className="nav nav-pills nav-justified">
                  <li>
                    <Link to={`/product/${item.id}`}>
                      <i className="fa fa-plus-square"></i> Detail
                    </Link>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        ))}
      </div>

      {page && page.last > 1 && (
        <ul className="pagination">
          <li className={page.current <= 1 ? "disabled" : ""}>
            <button
              className="btn btn-default"
              disabled={page.current <= 1}
              onClick={() => fetchAdvanced(page.current - 1)}
            >
              Prev
            </button>
          </li>
          <li style={{ margin: "0 10px" }}>
            {page.current} / {page.last}
          </li>
          <li className={page.current >= page.last ? "disabled" : ""}>
            <button
              className="btn btn-default"
              disabled={page.current >= page.last}
              onClick={() => fetchAdvanced(page.current + 1)}
            >
              Next
            </button>
          </li>
        </ul>
      )}
    </div>
  );
};

export default Search;
