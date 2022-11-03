CREATE VIEW trending_shops AS SELECT
    s.shop_id as id,
    s.name as name,
    s.zone as zone,
    mn.name as municipality,
    dp.name as department,
    s.phone_number as phone_number,
    s.description as description,
    s.disabled as disabled,
    COALESCE(c.comments, 0) as comments,
    COALESCE(r.average_rating, 0) as average_rating
FROM
    shops s
JOIN
    municipalities mn USING (municipality_id)
JOIN
    departments dp USING (department_id)
LEFT JOIN
    (SELECT avg(rating) as average_rating, shop_id FROM shop_ratings GROUP BY shop_id) r USING (shop_id)
LEFT JOIN
    (SELECT count(*) as comments, shop_id FROM comments GROUP BY shop_id) c USING (shop_id)
ORDER BY
    (comments * average_rating) DESC;

CREATE VIEW top_rated_shops AS SELECT
    s.shop_id as id,
    s.name as name,
    s.zone as zone,
    mn.name as municipality,
    dp.name as department,
    s.phone_number as phone_number,
    s.description as description,
    s.disabled as disabled,
    COALESCE(c.comments, 0) as comments,
    COALESCE(r.average_rating, 0) as average_rating
FROM
    shops s
JOIN
    municipalities mn USING (municipality_id)
JOIN
    departments dp USING (department_id)
LEFT JOIN
    (SELECT avg(rating) as average_rating, shop_id FROM shop_ratings GROUP BY shop_id) r USING (shop_id)
LEFT JOIN
    (SELECT count(*) as comments, shop_id FROM comments GROUP BY shop_id) c USING (shop_id)
ORDER BY
    average_rating DESC;

CREATE VIEW recent_shops AS SELECT
    s.shop_id as id,
    s.name as name,
    s.zone as zone,
    mn.name as municipality,
    dp.name as department,
    s.phone_number as phone_number,
    s.description as description,
    s.disabled as disabled,
    COALESCE(c.comments, 0) as comments,
    COALESCE(r.average_rating, 0) as average_rating
FROM
    shops s
JOIN
    municipalities mn USING (municipality_id)
JOIN
    departments dp USING (department_id)
LEFT JOIN
    (SELECT avg(rating) as average_rating, shop_id FROM shop_ratings GROUP BY shop_id) r USING (shop_id)
LEFT JOIN
    (SELECT count(*) as comments, shop_id FROM comments GROUP BY shop_id) c USING (shop_id)
ORDER BY
    s.updated_at DESC;

CREATE VIEW trending_products AS SELECT
p.product_id as id,
p.name as name,
p.price as price,
p.description as description,
p.disabled as disabled,
COALESCE(c.comments, 0) as comments,
COALESCE(r.average_rating, 0) as average_rating
FROM
    products p
JOIN
    shops s USING (shop_id)
LEFT JOIN
    (SELECT avg(rating) as average_rating, product_id FROM product_ratings GROUP BY product_id) r USING (product_id)
LEFT JOIN
    (SELECT count(*) as comments, product_id FROM comments GROUP BY product_id) c USING (product_id)
ORDER BY
    (comments * average_rating) DESC;

CREATE VIEW top_rated_products AS SELECT
    p.product_id as id,
    p.name as name,
    p.price as price,
    p.description as description,
    p.disabled as disabled,
    COALESCE(c.comments, 0) as comments,
    COALESCE(r.average_rating, 0) as average_rating
FROM
    products p
JOIN
    shops s USING (shop_id)
LEFT JOIN
    (SELECT avg(rating) as average_rating, product_id FROM product_ratings GROUP BY product_id) r USING (product_id)
LEFT JOIN
    (SELECT count(*) as comments, product_id FROM comments GROUP BY product_id) c USING (product_id)
ORDER BY
    average_rating DESC;

CREATE VIEW recent_products AS SELECT
    p.product_id as id,
    p.name as name,
    p.price as price,
    p.description as description,
    p.disabled as disabled,
    COALESCE(c.comments, 0) as comments,
    COALESCE(r.average_rating, 0) as average_rating
FROM
    products p
JOIN
    shops s USING (shop_id)
LEFT JOIN
    (SELECT avg(rating) as average_rating, product_id FROM product_ratings GROUP BY product_id) r USING (product_id)
LEFT JOIN
    (SELECT count(*) as comments, product_id FROM comments GROUP BY product_id) c USING (product_id)
ORDER BY
    p.updated_at DESC;