import React, { useEffect, useState } from "react";
import axios from "axios";
import { Link } from 'react-router-dom'
const Blog = () => {
  const [blogs, setBlogs] = useState([]);

  useEffect(() => {
    const fetchBlogs = async () => {
      try {
        const res = await axios.get(
          "http://localhost/laravel8/laravel8/public/api/blog"
        );
        setBlogs(res.data.blog.data); 
      } catch (error) {
        console.log("error:", error);
      }
    };

    fetchBlogs();
  }, []);

  return (
    <div>
      <section>
        <div className="container">
          <div className="row">
            <div className="col-sm-9">
              <div className="blog-post-area">
                <h2 className="title text-center">Latest From our Blog</h2>


                {blogs.map((item) => (
                  <div className="single-blog-post" key={item.id}>
                    <h3>{item.title}</h3>

                    <div className="post-meta">
                      <ul>
                        <li><i className="fa fa-user" /> Admin</li>
                        <li><i className="fa fa-calendar" /> {item.created_at}</li>
                      </ul>
                    </div>

                    <a href="">
                      <img
                        src={`http://localhost/laravel8/laravel8/public/upload/blog/image/${item.image}`}
                        alt={item.title}
                      />
                    </a>

                    <p>{item.description}</p>

                    <Link to={`/blog_detail/${item.id}`}>Read more</Link>
                  </div>
                ))}

                <div className="pagination-area">
                  <ul className="pagination">
                    <li><a className="active">1</a></li>
                  </ul>
                </div>

            </div>
        </div>
        </div>
    </div>
      </section>
    </div>
  );
};

export default Blog;

