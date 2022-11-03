export function getNavItems(component, role) {
  if (role == 'visitor') {
    return [
      {
        title: "Homepage",
        icon: "mdi-home",
        path: "/",
      },
      {
        title: "Login",
        icon: "mdi-login",
        path: "/login/",
      },
      {
        title: "Register",
        icon: "mdi-account",
        path: "/register/",
      },
    ]
  } else if (role == 'user') {
    return [
      {
        title: "Homepage",
        icon: "mdi-home",
        path: "/",
      },
      {
        title: "My Shop",
        icon: "mdi-shopping",
        path: "/my/shop/",
      },
      {
        title: "Logout",
        icon: "mdi-logout",
        action: () => component.logout(),
      },
    ]
  } else if (role == 'employee') {
    return [
      {
        title: "Homepage",
        icon: "mdi-home",
        path: "/",
      },
      {
        title: "My Shop",
        icon: "mdi-shopping",
        path: "/my/shop/",
      },
      {
        title: "Category Editor",
        icon: "mdi-tag",
        path: "/categories/",
      },
      {
        title: "Moderation",
        icon: "mdi-shield-sword",
        path: "/moderation/dashboard/",
      },
      {
        title: "Logout",
        icon: "mdi-logout",
        method: () => component.logout(),
      },
    ]
  } else if (role == 'admin') {
    return [
      {
        title: "Homepage",
        icon: "mdi-home",
        path: "/",
      },
      {
        title: "My Shop",
        icon: "mdi-shopping",
        path: "/my/shop/",
      },
      {
        title: "Category Editor",
        icon: "mdi-tag",
        path: "/categories/",
      },
      {
        title: "Moderation",
        icon: "mdi-shield-sword",
        path: "/moderation/dashboard/",
      },
      {
        title: "Manage Users",
        icon: "mdi-badge-account-horizontal",
        path: "/admin/users/",
      },
      {
        title: "Reports",
        icon: "mdi-file-document",
        path: "/reports/dashboard/",
      },
      {
        title: "Homepage Editor",
        icon: "mdi-home",
        path: "/home/edit/",
      },
      {
        title: "Logout",
        icon: "mdi-logout",
        action: () => component.logout(),
      },
    ]
  }
}