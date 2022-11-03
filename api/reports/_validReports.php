<?php

if (realpath(__FILE__) == realpath($_SERVER["SCRIPT_FILENAME"])) {
    require_once '../utils/request.php';
    $req->fail("Forbidden", 403);
}

$validReports = [
    [
        'name' => 'Users',
        'table' => 'users',
        'columns' => [
            ['name' => 'ID', 'column' => 'id', 'type' => 'number',],
            ['name' => 'E-mail', 'column' => 'email', 'type' => 'text',],
            ['name' => 'Display name', 'column' => 'display_name', 'type' => 'text',],
            ['name' => 'Role', 'column' => 'role', 'type' => 'text',],
            ['name' => 'Created at', 'column' => 'created_at', 'type' => 'date',],
            ['name' => 'Updated at', 'column' => 'updated_at', 'type' => 'date',],
        ],
    ],
    [
        'name' => 'Products',
        'table' => 'products',
        'columns' => [
            ['name' => 'ID', 'column' => 'id', 'type' => 'number',],
            ['name' => 'Name', 'column' => 'name', 'type' => 'text',],
            ['name' => 'Price', 'column' => 'price', 'type' => 'number',],
            ['name' => 'Description', 'column' => 'description', 'type' => 'text',],
            ['name' => 'Disabled', 'column' => 'disabled', 'type' => 'boolean',],
            ['name' => 'Comments', 'column' => 'comments', 'type' => 'number',],
            ['name' => 'Average rating', 'column' => 'average_rating', 'type' => 'number',],
            ['name' => 'Shop ID', 'column' => 'shop_id', 'type' => 'number',],
            ['name' => 'Shop Name', 'column' => 'shop_name', 'type' => 'text',],
            ['name' => 'Owner ID', 'column' => 'owner_id', 'type' => 'number',],
            ['name' => 'Owner Name', 'column' => 'owner_name', 'type' => 'text',],
            ['name' => 'Created at', 'column' => 'created_at', 'type' => 'date',],
            ['name' => 'Updated at', 'column' => 'updated_at', 'type' => 'date',],
        ],
    ],
    [
        'name' => 'Shops',
        'table' => 'shops',
        'columns' => [
            ['name' => 'ID', 'column' => 'id', 'type' => 'number',],
            ['name' => 'Name', 'column' => 'name', 'type' => 'text',],
            ['name' => 'Zone', 'column' => 'zone', 'type' => 'number',],
            ['name' => 'Municipality', 'column' => 'municipality', 'type' => 'text',],
            ['name' => 'Department', 'column' => 'department', 'type' => 'text',],
            ['name' => 'Latitude', 'column' => 'latitude', 'type' => 'number',],
            ['name' => 'Longitude', 'column' => 'longitude', 'type' => 'number',],
            ['name' => 'Phone number', 'column' => 'phone_number', 'type' => 'text',],
            ['name' => 'Description', 'column' => 'description', 'type' => 'text',],
            ['name' => 'Disabled', 'column' => 'disabled', 'type' => 'boolean',],
            ['name' => 'Total products', 'column' => 'total_products', 'type' => 'number',],
            ['name' => 'Enabled products', 'column' => 'enabled_products', 'type' => 'number',],
            ['name' => 'Comments', 'column' => 'comments', 'type' => 'number',],
            ['name' => 'Average rating', 'column' => 'average_rating', 'type' => 'number',],
            ['name' => 'Owner ID', 'column' => 'owner_id', 'type' => 'number',],
            ['name' => 'Owner Name', 'column' => 'owner_name', 'type' => 'text',],
            ['name' => 'Created at', 'column' => 'created_at', 'type' => 'date',],
            ['name' => 'Updated at', 'column' => 'updated_at', 'type' => 'date',],
        ],
    ],
    [
        'name' => 'Comments',
        'table' => 'comments',
        'columns' => [
            ['name' => 'ID', 'column' => 'id', 'type' => 'number',],
            ['name' => 'Author ID', 'column' => 'author_id', 'type' => 'number',],
            ['name' => 'Author name', 'column' => 'author_name', 'type' => 'text',],
            ['name' => 'Content', 'column' => 'content', 'type' => 'text',],
            ['name' => 'Parent comment ID', 'column' => 'parent_comment_id', 'type' => 'number',],
            ['name' => 'Parent comment author ID', 'column' => 'parent_comment_author_id', 'type' => 'number',],
            ['name' => 'Parent comment author name', 'column' => 'parent_comment_author_name', 'type' => 'text',],
            ['name' => 'Replies', 'column' => 'replies', 'type' => 'number',],
            ['name' => 'Votes', 'column' => 'votes', 'type' => 'number',],
            ['name' => 'Created at', 'column' => 'created_at', 'type' => 'date',],
        ],
    ],
    [
        'name' => 'Moderation events',
        'table' => 'moderation_events',
        'columns' => [
            ['name' => 'ID', 'column' => 'id', 'type' => 'number',],
            ['name' => 'Moderator ID', 'column' => 'moderator_id', 'type' => 'number',],
            ['name' => 'Moderator name', 'column' => 'moderator_name', 'type' => 'text',],
            ['name' => 'Item owner ID', 'column' => 'item_owner_id', 'type' => 'number',],
            ['name' => 'Item owner name', 'column' => 'item_owner_name', 'type' => 'text',],
            ['name' => 'Item ID', 'column' => 'item_id', 'type' => 'number',],
            ['name' => 'Item type', 'column' => 'item_type', 'type' => 'text',],
            ['name' => 'Item name', 'column' => 'item_name', 'type' => 'text',],
            ['name' => 'Item description', 'column' => 'item_description', 'type' => 'text',],
            ['name' => 'Item creation date', 'column' => 'item_created_at', 'type' => 'date',],
            ['name' => 'Item update date', 'column' => 'item_updated_at', 'type' => 'date',],
            ['name' => 'Moderation status', 'column' => 'moderation_status', 'type' => 'text',],
            ['name' => 'Reason', 'column' => 'reason', 'type' => 'text',],
            ['name' => 'Moderated at', 'column' => 'moderated_at', 'type' => 'date',],
        ],
    ],
];

$baseQueries = [
    'users' => 'SELECT
        u.user_id as id,
        u.email as email,
        u.display_name as display_name,
        u.role as role,
        u.created_at as created_at,
        u.updated_at as updated_at
    FROM
        users u',
    'products' => 'SELECT
        p.product_id as id,
        p.name as name,
        p.price as price,
        p.description as description,
        p.disabled as disabled,
        COALESCE(c.comments, 0) as comments,
        COALESCE(r.average_rating, 0) as average_rating,
        s.shop_id as shop_id,
        s.name as shop_name,
        u.user_id as owner_id,
        u.display_name as owner_name,
        p.created_at as created_at,
        p.updated_at as updated_at
    FROM
        products p
    JOIN
        shops s USING (shop_id)
    JOIN
        users u USING (user_id)
    LEFT JOIN
        (SELECT avg(rating) as average_rating, product_id FROM product_ratings GROUP BY product_id) r USING (product_id)
    LEFT JOIN
        (SELECT count(*) as comments, product_id FROM comments GROUP BY product_id) c USING (product_id)',
    'shops' => 'SELECT
        s.shop_id as id,
        s.name as name,
        s.zone as zone,
        mn.name as municipality,
        dp.name as department,
        s.latitude as latitude,
        s.longitude as longitude,
        s.phone_number as phone_number,
        s.description as description,
        s.disabled as disabled,
        COALESCE(pt.products, 0) as total_products,
        COALESCE(pe.products, 0) as enabled_products,
        COALESCE(c.comments, 0) as comments,
        COALESCE(r.average_rating, 0) as average_rating,
        u.user_id as owner_id,
        u.display_name as owner_name,
        s.created_at as created_at,
        s.updated_at as updated_at
    FROM
        shops s
    JOIN
        users u USING (user_id)
    JOIN
        municipalities mn USING (municipality_id)
    JOIN
        departments dp USING (department_id)
    LEFT JOIN
        (SELECT count(*) as products, shop_id FROM products GROUP BY shop_id) pt USING (shop_id)
    LEFT JOIN
        (SELECT count(*) as products, shop_id FROM products WHERE disabled = FALSE GROUP BY shop_id) pe USING (shop_id)
    LEFT JOIN
        (SELECT avg(rating) as average_rating, shop_id FROM shop_ratings GROUP BY shop_id) r USING (shop_id)
    LEFT JOIN
        (SELECT count(*) as comments, shop_id FROM comments GROUP BY shop_id) c USING (shop_id)',
    'comments' => 'SELECT
        c.comment_id as id,
        u.user_id as author_id,
        u.display_name as author_name,
        c.content as content,
        c.parent_comment_id as parent_comment_id,
        pc.author_id as parent_comment_author_id,
        pc.author_name as parent_comment_author_name,
        COALESCE(rc.replies, 0) as replies,
        COALESCE(cv.total_votes, 0) as votes,
        c.created_at as created_at
    FROM
        comments c
    JOIN
        users u ON c.author_id = u.user_id
    LEFT JOIN
        (SELECT sum(value) as total_votes, comment_id FROM comment_votes GROUP BY comment_id) cv USING (comment_id)
    LEFT JOIN
        (SELECT c.comment_id, u.user_id as author_id, u.display_name as author_name FROM comments c JOIN users u ON c.author_id = u.user_id) pc ON c.parent_comment_id = pc.comment_id
    LEFT JOIN
        (SELECT count(*) as replies, parent_comment_id FROM comments GROUP BY parent_comment_id) rc ON c.comment_id = rc.parent_comment_id',
    'moderation_events' => 'SELECT
        me.moderation_event_id as id,
        me.user_id as moderator_id,
        mu.display_name as moderator_name,
        me.item_owner_id as item_owner_id,
        iou.display_name as item_owner_name,
        me.item_id as item_id,
        me.item_type as item_type,
        me.item_name as item_name,
        me.item_description as item_description,
        me.item_created_at as item_created_at,
        me.item_updated_at as item_updated_at,
        IF(me.published, "Published", "Rejected") as moderation_status,
        me.reason as reason,
        me.date as moderated_at
    FROM
        moderation_events me
    JOIN
        users mu USING (user_id)
    JOIN
        users iou ON me.item_owner_id = u.user_id',
];
